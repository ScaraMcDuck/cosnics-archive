<?php // $Id$
/*
============================================================================== 
	Dokeos - elearning and course management software
	
	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) various contributors
	
	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	See the GNU General Public License for more details.
	
	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
============================================================================== 
*/
/**
==============================================================================
* This is the course creation library for Dokeos.
* It contains functions to create a course.
* Include/require it in your code to use its functionality.
*
* @package dokeos.library
* @todo clean up horrible structure, script is unwieldy, for example easier way to deal with
* different tool visibility settings: ALL_TOOLS_INVISIBLE, ALL_TOOLS_VISIBLE, CORE_TOOLS_VISIBLE...
==============================================================================
*/

include_once (api_get_library_path().'/database.lib.php');

/*
============================================================================== 
		FUNCTIONS
============================================================================== 
*/ 

/**
* Not tested yet. 
* We need this new function so not every script that creates courses needs
* to be changed when the behaviour necessary to create a course changes.
* This will reduce bugs.
*
* @return true if the course creation was succesful, false otherwise.
*/
function create_course($wanted_code, $title, $tutor_name, $category_code, $course_language, $course_admin_id, $dbNamePrefix, $firstExpirationDelay)
{
	$keys = define_course_keys($wanted_code, "", $dbNamePrefix);
	if (sizeof($keys))
	{
		$visual_code = $keys["currentCourseCode"];
		$code = $keys["currentCourseId"];
		$db_name = $keys["currentCourseDbName"];
		$directory = $keys["currentCourseRepository"];
		$expiration_date = time() + $firstExpirationDelay;
		prepare_course_repository($directory, $code);
		update_Db_course($db_name);
		fill_course_repository($directory);
		fill_Db_course($db_name, $directory, $course_language);
		add_course_role_right_location_values($code);
		register_course($code, $visual_code, $directory, $db_name, $tutor_name, $category_code, $title, $course_language, $course_admin_id, $expiration_date);
		return true;
	}
	else return false;
}

/**
* Add values for the new course to the table that holds the
* role - right - location relation.
* 
* All rights for the new course are copied from a default set of course rights,
* which the platform admin can edit.
* So for every newly created course, the value of the location / right / role combination is
* - at the start at least - the exact same value as the location / right / role combination
* for the default course.
*/
function add_course_role_right_location_values($course_code)
{
	$location_table = Database::get_main_table(MAIN_LOCATION_TABLE);
	$relation_table = Database::get_main_table(MAIN_ROLE_RIGHT_LOCATION_TABLE);
		
	//get default course locations, store into $default_location_list array
	$course_location = "|course,DEFAULT";
	$get_default_location_sql = "SELECT * FROM $location_table WHERE ( location LIKE '%$course_location|%' ) OR ( location LIKE '%$course_location' )";
	
	$sql_result = api_sql_query($get_default_location_sql);
	while ($result = mysql_fetch_array($sql_result, MYSQL_ASSOC) )
	{
		$default_location_list[] = $result;
	}
	
	//for each default location, retrieve value and
	//store info for new course code
	foreach ($default_location_list as $default_location)
	{
		//create new location by replacing default course code with new course code
		$new_location = str_replace("|course,DEFAULT", "|course,$course_code", $default_location['location']);
		$add_new_location_sql = "INSERT INTO $location_table ( `id` , `location` ) VALUES ('', '$new_location')";
		api_sql_query($add_new_location_sql);
		$new_location_id = mysql_insert_id();
		
		//find all relations in the table that contained the default location id
		$find_relations_query = "SELECT * FROM $relation_table WHERE location_id='".$default_location['id']."'";
		$sql_result = api_sql_query($find_relations_query);
		while ($relation_result = mysql_fetch_array($sql_result, MYSQL_ASSOC) )
		{
			//insert new relation with same role id, same right id, new location id, and same value
			$add_new_relation_sql = "INSERT INTO ".$relation_table." VALUES ('".$relation_result['role_id']."', '".$relation_result['right_id']."', '$new_location_id', '".$relation_result['value']."')";
			api_sql_query($add_new_relation_sql);
		}
	}
}

/**
 *	Defines the four needed keys to create a course based on several parameters.
 *	@return array with the needed keys ["currentCourseCode"], ["currentCourseId"],  ["currentCourseDbName"], ["currentCourseRepository"]
 *
 * @param	$wantedCode the code you want for this course
 * @param	string prefix //  prefix added for ALL keys
 * @todo	eliminate globals
 */
function define_course_keys($wantedCode, $prefix4all = "", $prefix4baseName = "", $prefix4path = "", $addUniquePrefix = false, $useCodeInDepedentKeys = true)
{
	global $rootSys, $coursesRepositoryAppend, $prefixAntiNumber, $singleDbEnabled, $mainDbName, $courseTablePrefix, $dbGlu;

	$course_table = Database :: get_main_table(MAIN_COURSE_TABLE);

	$wantedCode = strtr($wantedCode, "��������������������������", "AAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");

	$wantedCode = ereg_replace("[^A-Z0-9]", "", strtoupper($wantedCode));

	if (empty ($wantedCode))
	{
		$wantedCode = "CL";
	}
	$keysCourseCode = $wantedCode;

	if (!$useCodeInDepedentKeys)
	{
		$wantedCode = '';
	}

	if ($addUniquePrefix)
	{
		$uniquePrefix = substr(md5(uniqid(rand())), 0, 10);
	}
	else
	{
		$uniquePrefix = '';
	}

	if ($addUniqueSuffix)
	{
		$uniqueSuffix = substr(md5(uniqid(rand())), 0, 10);
	}
	else
	{
		$uniqueSuffix = '';
	}

	$keys = array ();

	$finalSuffix = array ('CourseId' => '', 'CourseDb' => '', 'CourseDir' => '');

	$limitNumbTry = 100;

	$keysAreUnique = false;

	$tryNewFSCId = $tryNewFSCDb = $tryNewFSCDir = 0;

	while (!$keysAreUnique)
	{
		$keysCourseId = $prefix4all.$uniquePrefix.$wantedCode.$uniqueSuffix.$finalSuffix['CourseId'];

		$keysCourseDbName = $prefix4baseName.$uniquePrefix.strtoupper($keysCourseId).$uniqueSuffix.$finalSuffix['CourseDb'];

		$keysCourseRepository = $prefix4path.$uniquePrefix.$wantedCode.$uniqueSuffix.$finalSuffix['CourseDir'];

		$keysAreUnique = true;

		// check if they are unique
		$query = "SELECT 1 FROM ".$course_table." WHERE code='".$keysCourseId."' LIMIT 0,1";
		$result = api_sql_query($query, __FILE__, __LINE__);

		if ($keysCourseId == DEFAULT_COURSE || mysql_num_rows($result))
		{
			$keysAreUnique = false;

			$tryNewFSCId ++;

			$finalSuffix['CourseId'] = substr(md5(uniqid(rand())), 0, 4);
		}

		if ($singleDbEnabled)
		{
			$query = "SHOW TABLES FROM `$mainDbName` LIKE '$courseTablePrefix$keysCourseDbName$dbGlu%'";
			$result = api_sql_query($query, __FILE__, __LINE__);
		}
		else
		{
			$query = "SHOW DATABASES LIKE '$keysCourseDbName'";
			$result = api_sql_query($query, __FILE__, __LINE__);
		}

		if (mysql_num_rows($result))
		{
			$keysAreUnique = false;

			$tryNewFSCDb ++;

			$finalSuffix['CourseDb'] = substr('_'.md5(uniqid(rand())), 0, 4);
		}

		if (file_exists($rootSys.$coursesRepositoryAppend.$keysCourseRepository))
		{
			$keysAreUnique = false;

			$tryNewFSCDir ++;

			$finalSuffix['CourseDir'] = substr(md5(uniqid(rand())), 0, 4);
		}

		if (($tryNewFSCId + $tryNewFSCDb + $tryNewFSCDir) > $limitNumbTry)
		{
			return $keys;
		}
	}

	// db name can't begin with a number
	if (!stristr("abcdefghijklmnopqrstuvwxyz", $keysCourseDbName[0]))
	{
		$keysCourseDbName = $prefixAntiNumber.$keysCourseDbName;
	}

	$keys["currentCourseCode"] = $keysCourseCode;
	$keys["currentCourseId"] = $keysCourseId;
	$keys["currentCourseDbName"] = $keysCourseDbName;
	$keys["currentCourseRepository"] = $keysCourseRepository;

	return $keys;
}

/**
 *
 *
 */
function prepare_course_repository($courseRepository, $courseId)
{
	GLOBAL $coursesRepositorySys;
	umask(0);
	mkdir($coursesRepositorySys.$courseRepository, 0777);
	mkdir($coursesRepositorySys.$courseRepository."/document", 0777);
	mkdir($coursesRepositorySys.$courseRepository."/dropbox", 0777);
	mkdir($coursesRepositorySys.$courseRepository."/group", 0777);
	mkdir($coursesRepositorySys.$courseRepository."/page", 0777);
	mkdir($coursesRepositorySys.$courseRepository."/scorm", 0777);
	mkdir($coursesRepositorySys.$courseRepository."/temp", 0777);
	mkdir($coursesRepositorySys.$courseRepository."/work", 0777);

	//create .htaccess in dropbox
	$fp = fopen($coursesRepositorySys.$courseRepository."/dropbox/.htaccess", "w");
	fwrite($fp, "AuthName AllowLocalAccess
	                             AuthType Basic
	
	                             order deny,allow
	                             deny from all
	
	                             php_flag zlib.output_compression off");
	fclose($fp);

	// build index.php of course
	$fd = fopen($coursesRepositorySys.$courseRepository."/index.php", "w");

	// str_replace() removes \r that cause squares to appear at the end of each line
	$string = str_replace("\r", "", "<?"."php
	\$cidReq = \"$courseId\";
	\$dbname = \"$courseId\";
	
	include(\"../../main/course_home/course_home.php\");
	?>");
	fwrite($fd, "$string");
	$fd = fopen($coursesRepositorySys.$courseRepository."/group/index.php", "w");
	$string = "<html></html>";
	fwrite($fd, "$string");
	return 0;
};

function update_Db_course($courseDbName)
{
	global $singleDbEnabled, $courseTablePrefix, $dbGlu;

	if (!$singleDbEnabled)
	{
		api_sql_query("CREATE DATABASE IF NOT EXISTS `$courseDbName`", __FILE__, __LINE__);
	}

	$courseDbName = $courseTablePrefix.$courseDbName.$dbGlu;

	$TABLECOURSEHOMEPAGE = $courseDbName."tool";
	$TABLEINTROS = $courseDbName."tool_intro";

	$TABLEGROUPS = $courseDbName."group_info";
	$TABLEGROUPCATEGORIES = $courseDbName."group_category";
	$TABLEGROUPUSER = $courseDbName."group_rel_user";

	$TABLEITEMPROPERTY = $courseDbName."item_property";

	$TABLETOOLUSERINFOCONTENT = $courseDbName."userinfo_content";
	$TABLETOOLUSERINFODEF = $courseDbName."userinfo_def";

	$TABLETOOLCOURSEDESC = $courseDbName."course_description";
	$TABLETOOLAGENDA = $courseDbName."calendar_event";
	$TABLETOOLANNOUNCEMENTS = $courseDbName."announcement";
	$TABLEADDEDRESOURCES = $courseDbName."resource";
	$TABLETOOLWORKS = $courseDbName."student_publication";
	$TABLETOOLWORKSUSER = $courseDbName."stud_pub_rel_user";
	$TABLETOOLDOCUMENT = $courseDbName."document";
	$TABLETOOLSCORMDOCUMENT = $courseDbName."scormdocument";

	$TABLETOOLLINK = $courseDbName."link";
	$TABLETOOLLINKCATEGORIES = $courseDbName."link_category";

	$TABLETOOLONLINECONNECTED = $courseDbName."online_connected";
	$TABLETOOLONLINELINK = $courseDbName."online_link";

	$TABLETOOLCHATCONNECTED = $courseDbName."chat_connected";

	$TABLEQUIZ = $courseDbName."quiz";
	$TABLEQUIZQUESTION = $courseDbName."quiz_rel_question";
	$TABLEQUIZQUESTIONLIST = $courseDbName."quiz_question";
	$TABLEQUIZANSWERSLIST = $courseDbName."quiz_answer";

	$TABLEPHPBBACCESS = $courseDbName."bb_access";
	$TABLEPHPBBBANLIST = $courseDbName."bb_banlist";
	$TABLEPHPBBCATEGORIES = $courseDbName."bb_categories";
	$TABLEPHPBBCONFIG = $courseDbName."bb_config";
	$TABLEPHPBBDISALLOW = $courseDbName."bb_disallow";
	$TABLEPHPBBFORUMACCESS = $courseDbName."bb_forum_access";
	$TABLEPHPBBFORUMMODS = $courseDbName."bb_forum_mods";
	$TABLEPHPBBFORUMS = $courseDbName."bb_forums";
	$TABLEPHPBBHEADFOOT = $courseDbName."bb_headermetafooter";
	$TABLEPHPBBDOMAINLIST = $courseDbName."liste_domaines";
	$TABLEPHPBBPAGES = $courseDbName."page";
	$TABLEPHPBBPOSTS = $courseDbName."bb_posts";
	$TABLEPHPBBPOSTSTEXT = $courseDbName."bb_posts_text";
	$TABLEPHPBBPRIVMSG = $courseDbName."bb_priv_msgs";
	$TABLEPHPBBRANK = $courseDbName."bb_ranks";
	$TABLEPHPBBSESSIONS = $courseDbName."bb_sessions";
	$TABLEPHPBBTHEMES = $courseDbName."bb_themes";
	$TABLEPHPBBTOPICS = $courseDbName."bb_topics";
	$TABLEPHPBBUSERS = $courseDbName."bb_users";
	$TABLEPHPBBWHOSONLINE = $courseDbName."bb_whosonline";
	$TABLEPHPBBWORDS = $courseDbName."bb_words";

	$TABLETOOLDROPBOXPOST = $courseDbName."dropbox_post";
	$TABLETOOLDROPBOXFILE = $courseDbName."dropbox_file";
	$TABLETOOLDROPBOXPERSON = $courseDbName."dropbox_person";

	$TABLELEARNPATHITEMS = $courseDbName."learnpath_item";
	$TABLELEARNPATHCHAPTERS = $courseDbName."learnpath_chapter";
	$TABLELEARNPATHMAIN = $courseDbName."learnpath_main";
	$TABLELEARNPATHUSERS = $courseDbName."learnpath_user";

	/*
	-----------------------------------------------------------
		Announcement tool
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLETOOLANNOUNCEMENTS."` (
		id mediumint unsigned NOT NULL auto_increment,
		title text, 
		content text,
		end_date date default NULL,
		display_order mediumint NOT NULL default 0,
		PRIMARY KEY  (id)
		) TYPE=MyISAM";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Resources
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLEADDEDRESOURCES."` (
		id int unsigned NOT NULL auto_increment,
		source_type varchar(50) default NULL,
		source_id int unsigned default NULL,
		resource_type varchar(50) default NULL,
		resource_id int unsigned default NULL,
		UNIQUE KEY id (id)
		) TYPE=MyISAM";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Learning path
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLELEARNPATHITEMS."` (
		id int unsigned NOT NULL auto_increment,
		chapter_id int unsigned default NULL,
		item_type varchar(50) default NULL,
		item_id int unsigned default NULL,
		display_order smallint default NULL,
		title varchar(255) default NULL,
		description text,
		prereq_id int unsigned default NULL,
		prereq_type char(1) default NULL,
		prereq_completion_limit varchar(10) default NULL,
		UNIQUE KEY id (id)
		) TYPE=MyISAM";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLELEARNPATHCHAPTERS."` (
		id int unsigned NOT NULL auto_increment,
		learnpath_id int unsigned default NULL,
		chapter_name varchar(255) default NULL,
		chapter_description text,
	  	parent_chapter_id int unsigned default 0 NOT NULL,
		display_order mediumint unsigned NOT NULL default 0,
		PRIMARY KEY  (id)
		) TYPE=MyISAM";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLELEARNPATHMAIN."` (
		learnpath_id int unsigned NOT NULL auto_increment,
		learnpath_name varchar(255) default NULL,
		learnpath_description text,
		PRIMARY KEY  (learnpath_id)
		) TYPE=MyISAM";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLELEARNPATHUSERS."` (
		user_id int unsigned NOT NULL,
		learnpath_id int unsigned NOT NULL,
		learnpath_item_id int unsigned default NULL,
		status varchar(15) default NULL,
		score smallint default NULL,
		time varchar(20) default NULL
		) TYPE=MyISAM";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLETOOLUSERINFOCONTENT."` (
		id int unsigned NOT NULL auto_increment,
		user_id int unsigned NOT NULL,
		definition_id int unsigned NOT NULL,
		editor_ip varchar(39) default NULL,
		edition_time datetime default NULL,
		content text NOT NULL,
		PRIMARY KEY  (id),
		KEY user_id (user_id)
		) TYPE=MyISAM";

	api_sql_query($sql, __FILE__, __LINE__);

	// Unused table. Temporarily ignored for tests.
	// Reused because of user/userInfo and user/userInfoLib scripts
	$sql = "
		CREATE TABLE `".$TABLETOOLUSERINFODEF."` (
		id int unsigned NOT NULL auto_increment,
		title varchar(80) NOT NULL default '',
		comment text,
		line_count tinyint unsigned NOT NULL default 5,
		rank tinyint unsigned NOT NULL default 0,
		PRIMARY KEY  (id)
		) TYPE=MyISAM";

	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Forum tool
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLEPHPBBACCESS."` (
		access_id int(10) NOT NULL auto_increment,
		access_title varchar(20),
		PRIMARY KEY (access_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLEPHPBBBANLIST."` (
		ban_id int(10) NOT NULL auto_increment,
		ban_userid int(10),
		ban_ip varchar(16),
		ban_start int(32),
		ban_end int(50),
		ban_time_type int(10),
		PRIMARY KEY (ban_id),
		KEY ban_id (ban_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLEPHPBBCATEGORIES."` (
		cat_id int(10) NOT NULL auto_increment,
		cat_title varchar(100),
		cat_order varchar(10),
		PRIMARY KEY (cat_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLEPHPBBCONFIG."` (
		config_id int(10) NOT NULL auto_increment,
		sitename varchar(100),
		allow_html int(2),
		allow_bbcode int(2),
		allow_sig int(2),
		allow_namechange int(2) DEFAULT '0',
		admin_passwd varchar(32),
		selected int(2) DEFAULT '0' NOT NULL,
		posts_per_page int(10),
		hot_threshold int(10),
		topics_per_page int(10),
		allow_theme_create int(10),
		override_themes int(2) DEFAULT '0',
		email_sig varchar(255),
		email_from varchar(100),
		default_lang varchar(255),
		PRIMARY KEY (config_id),
		UNIQUE selected (selected)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLEPHPBBDISALLOW."`(
		disallow_id int(10) NOT NULL auto_increment,
		disallow_username varchar(50),
		PRIMARY KEY (disallow_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLEPHPBBFORUMACCESS."`(
		forum_id int(10) DEFAULT '0' NOT NULL,
		user_id int (10) DEFAULT '0' NOT NULL,
		can_post tinyint(1) DEFAULT '0' NOT NULL,
		PRIMARY KEY (forum_id, user_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLEPHPBBFORUMMODS."`(
		forum_id int(10) DEFAULT '0' NOT NULL,
		user_id int(10) DEFAULT '0' NOT NULL
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLEPHPBBFORUMS."`(
		forum_id int(10) NOT NULL auto_increment,
		forum_name varchar(150),
		forum_desc text,
		forum_access int(10) DEFAULT '1',
		forum_moderator int(10),
		forum_topics int(10) DEFAULT '0' NOT NULL,
		forum_posts int(10) DEFAULT '0' NOT NULL,
		forum_last_post_id int(10) DEFAULT '0' NOT NULL,
		cat_id int(10),
		forum_type int(10) DEFAULT '0',
		PRIMARY KEY (forum_id),
		KEY forum_last_post_id (forum_last_post_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLEPHPBBHEADFOOT."`(
		header text,
		meta text,
		footer text
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLEPHPBBPOSTS."`(
		post_id int(10) NOT NULL auto_increment,
		topic_id int(10) DEFAULT '0' NOT NULL,
		forum_id int(10) DEFAULT '0' NOT NULL,
		poster_id int(10) DEFAULT '0' NOT NULL,
		post_time varchar(20),
		poster_ip varchar(16),
		nom varchar(30),
		prenom varchar(30),
		topic_notify tinyint(2),
		parent_id int(10) default '0',
		PRIMARY KEY (post_id),
		KEY post_id (post_id),
		KEY forum_id (forum_id),
		KEY topic_id (topic_id),
		KEY poster_id (poster_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	//  Structure de la table 'priv_msgs'
	$sql = "
		CREATE TABLE `".$TABLEPHPBBPRIVMSG."` (
		msg_id int(10) NOT NULL auto_increment,
		from_userid int(10) DEFAULT '0' NOT NULL,
		to_userid int(10) DEFAULT '0' NOT NULL,
		msg_time varchar(20),
		poster_ip varchar(16),
		msg_status int(10) DEFAULT '0',
		msg_text text,
		PRIMARY KEY (msg_id),
		KEY msg_id (msg_id),
		KEY to_userid (to_userid)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	//  Structure de la table 'ranks'
	$sql = "
		CREATE TABLE `".$TABLEPHPBBRANK."` (
		rank_id int(10) NOT NULL auto_increment,
		rank_title varchar(50) NOT NULL,
		rank_min int(10) DEFAULT '0' NOT NULL,
		rank_max int(10) DEFAULT '0' NOT NULL,
		rank_special int(2) DEFAULT '0',
		rank_image varchar(255),
		PRIMARY KEY (rank_id),
		KEY rank_min (rank_min),
		KEY rank_max (rank_max)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	//  structure de la table 'session'
	$sql = "
		CREATE TABLE `".$TABLEPHPBBSESSIONS."` (
		sess_id int(10) unsigned DEFAULT '0' NOT NULL,
		user_id int(10) DEFAULT '0' NOT NULL,
		start_time int(10) unsigned DEFAULT '0' NOT NULL,
		remote_ip varchar(15) NOT NULL,
		PRIMARY KEY (sess_id),
		KEY sess_id (sess_id),
		KEY start_time (start_time),
		KEY remote_ip (remote_ip)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	//  Structure de la table 'themes'
	$sql = "
		CREATE TABLE `".$TABLEPHPBBTHEMES."` (
		theme_id int(10) NOT NULL auto_increment,
		theme_name varchar(35),
		bgcolor varchar(10),
		textcolor varchar(10),
		color1 varchar(10),
		color2 varchar(10),
		table_bgcolor varchar(10),
		header_image varchar(50),
		newtopic_image varchar(50),
		reply_image varchar(50),
		linkcolor varchar(15),
		vlinkcolor varchar(15),
		theme_default int(2) DEFAULT '0',
		fontface varchar(100),
		fontsize1 varchar(5),
		fontsize2 varchar(5),
		fontsize3 varchar(5),
		fontsize4 varchar(5),
		tablewidth varchar(10),
		replylocked_image varchar(255),
		PRIMARY KEY (theme_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	//  Structure de la table 'topics'
	$sql = "
		CREATE TABLE `".$TABLEPHPBBTOPICS."` (
		topic_id int(10) NOT NULL auto_increment,
		topic_title varchar(100),
		topic_poster int(10),
		topic_time varchar(20),
		topic_views int(10) DEFAULT '0' NOT NULL,
		topic_replies int(10) DEFAULT '0' NOT NULL,
		topic_last_post_id int(10) DEFAULT '0' NOT NULL,
		forum_id int(10) DEFAULT '0' NOT NULL,
		topic_status int(10) DEFAULT '0' NOT NULL,
		topic_notify int(2) DEFAULT '0',
		nom varchar(30),
		prenom varchar(30),
		PRIMARY KEY (topic_id),
		KEY topic_id (topic_id),
		KEY forum_id (forum_id),
		KEY topic_last_post_id (topic_last_post_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	//  Structure de la table 'users'
	$sql = "
		CREATE TABLE `".$TABLEPHPBBUSERS."` (
		user_id int(10) NOT NULL auto_increment,
		username varchar(40) NOT NULL,
		user_regdate varchar(20) NOT NULL,
		user_password varchar(32) NOT NULL,
		user_email varchar(50),
		user_icq varchar(15),
		user_website varchar(100),
		user_occ varchar(100),
		user_from varchar(100),
		user_intrest varchar(150),
		user_sig varchar(255),
		user_viewemail tinyint(2),
		user_theme int(10),
		user_aim varchar(18),
		user_yim varchar(25),
		user_msnm varchar(25),
		user_posts int(10) DEFAULT '0',
		user_attachsig int(2) DEFAULT '0',
		user_desmile int(2) DEFAULT '0',
		user_html int(2) DEFAULT '0',
		user_bbcode int(2) DEFAULT '0',
		user_rank int(10) DEFAULT '0',
		user_level int(10) DEFAULT '1',
		user_lang varchar(255),
		user_actkey varchar(32),
		user_newpasswd varchar(32),
		PRIMARY KEY (user_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	//  Structure de la table 'whosonline'
	$sql = "
		CREATE TABLE `".$TABLEPHPBBWHOSONLINE."` (
		id int(3) NOT NULL auto_increment,
		ip varchar(255),
		name varchar(255),
		count varchar(255),
		online_date varchar(255),
		username varchar(40),
		forum int(10),
		PRIMARY KEY (id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	//  Structure de la table 'words'
	$sql = "
		CREATE TABLE `".$TABLEPHPBBWORDS."` (
		word_id int(10) NOT NULL auto_increment,
		word varchar(100),
		replacement varchar(100),
		PRIMARY KEY (word_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Exercise tool
	-----------------------------------------------------------
	*/
	// Exercise tool - Tests/exercises
	$sql = "
		CREATE TABLE `".$TABLEQUIZ."` (
		id mediumint unsigned NOT NULL auto_increment,
		title varchar(200) NOT NULL,
		description text default NULL,
		sound varchar(50) default NULL,
		type tinyint unsigned NOT NULL default 1,
		random smallint(6) NOT NULL default 0,
		active enum('0','1') NOT NULL default '0',
		PRIMARY KEY  (id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	// Exercise tool - questions
	$sql = "
		CREATE TABLE `".$TABLEQUIZQUESTIONLIST."` (
		id mediumint unsigned NOT NULL auto_increment,
		question varchar(200) NOT NULL,
		description text default NULL,
		ponderation smallint unsigned default NULL,
		position mediumint unsigned NOT NULL default 1,
		type tinyint unsigned NOT NULL default 2,
		picture varchar(50) default NULL,
		PRIMARY KEY  (id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	// Exercise tool - answers
	$sql = "
		CREATE TABLE `".$TABLEQUIZANSWERSLIST."` (
		id mediumint unsigned NOT NULL,
		question_id mediumint unsigned NOT NULL,
		answer text NOT NULL,
		correct mediumint unsigned default NULL,
		comment text default NULL,
		ponderation smallint default NULL,
		position mediumint unsigned NOT NULL default 1,
		PRIMARY KEY  (id, question_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	// Exercise tool - Test/question relations
	$sql = "
		CREATE TABLE `".$TABLEQUIZQUESTION."` (
		question_id mediumint unsigned NOT NULL,
		exercice_id mediumint unsigned NOT NULL,
		PRIMARY KEY  (question_id,exercice_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Course description
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLETOOLCOURSEDESC."` (
		id TINYINT UNSIGNED NOT NULL auto_increment,
		title VARCHAR(255),
		content TEXT,
		UNIQUE (id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Course homepage tool list
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLECOURSEHOMEPAGE."` (
		id int unsigned NOT NULL auto_increment,
		name varchar(100) NOT NULL,
		link varchar(255) NOT NULL,
		image varchar(100) default NULL,
		visibility tinyint unsigned default 0,
		admin varchar(200) default NULL,
		address varchar(120) default NULL,
		added_tool enum('0','1') default 1,
		target enum('_self','_blank') NOT NULL default '_self',
		PRIMARY KEY  (id)
		) TYPE=MyISAM";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Agenda tool
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLETOOLAGENDA."` (
		id int unsigned NOT NULL auto_increment,
		title varchar(200) NOT NULL,
		content text,
		start_date datetime NOT NULL default '0000-00-00 00:00:00',
		end_date datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY (id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Document tool
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLETOOLDOCUMENT."` (
			`id` int(10) unsigned NOT NULL auto_increment,
			`path` varchar(255) NOT NULL default '',
			`comment` text,
			`title` varchar(255) default NULL,
			`filetype` set('file','folder') NOT NULL default 'file',
			`size` int(16) NOT NULL default '0',
			PRIMARY KEY  (`id`)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Scorm Document tool
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLETOOLSCORMDOCUMENT."` (
		id int unsigned NOT NULL auto_increment,
		path varchar(255) NOT NULL,
		visibility char(1) DEFAULT 'v' NOT NULL,
		comment varchar(255),
		filetype set('file','folder') NOT NULL default 'file',
		name varchar(100),
		PRIMARY KEY (id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Student publications
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLETOOLWORKS."` (
		id int unsigned NOT NULL auto_increment,
		url varchar(200) default NULL,
		title varchar(200) default NULL,
		description varchar(250) default NULL,
		author varchar(200) default NULL,
		active tinyint default NULL,
		accepted tinyint default 0,
		sent_date datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY  (id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	// No reference to this table in code - commented temporarily
	/*
	$sql ="
	CREATE TABLE `".$TABLETOOLWORKSUSER."` (
	stud_pub_id int unsigned NOT NULL,
	user_id int unsigned NOT NULL,
	PRIMARY KEY  (stud_pub_id,user_id)
	)";
	api_sql_query($sql,__FILE__,__LINE__);
	*/
	/*
	-----------------------------------------------------------
		Links tool
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLETOOLLINK."` (
		id int unsigned NOT NULL auto_increment,
		url TEXT NOT NULL,
		title varchar(150) default NULL,
		description text,
		category_id smallint unsigned default NULL,
		display_order smallint unsigned NOT NULL default 0,
		on_homepage enum('0','1') NOT NULL default '0',
		PRIMARY KEY (id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLETOOLLINKCATEGORIES."` (
		id smallint unsigned NOT NULL auto_increment,
		category_title varchar(255) NOT NULL,
		description text,
		display_order mediumint unsigned NOT NULL default 0,
		PRIMARY KEY (id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Online
	-----------------------------------------------------------
	*/
	$sql = "
		CREATE TABLE `".$TABLETOOLONLINECONNECTED."` (
		user_id int unsigned NOT NULL,
		last_connection datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY  (user_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLETOOLONLINELINK."` (
		id smallint unsigned NOT NULL auto_increment,
		name char(50) NOT NULL default '',
		url char(100) NOT NULL,
		PRIMARY KEY  (id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	$sql = "
		CREATE TABLE `".$TABLETOOLCHATCONNECTED."` (
		user_id int unsigned NOT NULL default '0',
		last_connection datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY  (user_id)
		)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Groups tool
	-----------------------------------------------------------
	*/
	api_sql_query("CREATE TABLE `".$TABLEGROUPS."` (
		id int unsigned NOT NULL auto_increment,
		name varchar(100) default NULL,
		category_id int unsigned NOT NULL default 0,
		description text,
		tutor_id int unsigned default NULL,
		forum_state enum('0','1','2') NOT NULL default 1,
		forum_id int unsigned default NULL,
		max_student smallint unsigned NOT NULL default 8,
		doc_state enum('0','1','2') NOT NULL default 1,
		secret_directory varchar(255) default NULL,
		self_registration_allowed enum('0','1') NOT NULL default '0',
		self_unregistration_allowed enum('0','1') NOT NULL default '0',
		PRIMARY KEY  (id)
		)");

	api_sql_query("CREATE TABLE `".$TABLEGROUPCATEGORIES."` (
		id int unsigned NOT NULL auto_increment,
		title varchar(255) NOT NULL default '',
		description text NOT NULL,
		forum_state tinyint unsigned NOT NULL default 1,
		doc_state tinyint unsigned NOT NULL default 1,
		max_student smallint unsigned NOT NULL default 8,
		self_reg_allowed enum('0','1') NOT NULL default '0',
		self_unreg_allowed enum('0','1') NOT NULL default '0',
		groups_per_user smallint unsigned NOT NULL default 0,
		display_order smallint unsigned NOT NULL default 0,
		PRIMARY KEY  (id)
		)");

	api_sql_query("CREATE TABLE `".$TABLEGROUPUSER."` (
		id int unsigned NOT NULL auto_increment,
		user_id int unsigned NOT NULL,
		group_id int unsigned NOT NULL default 0,
		status int NOT NULL default 0,
		role char(50) NOT NULL,
		PRIMARY KEY  (id)
		)");

	api_sql_query("CREATE TABLE `".$TABLEITEMPROPERTY."` (
		tool varchar(100) NOT NULL default '',
		insert_user_id int unsigned NOT NULL default '0',
		insert_date datetime NOT NULL default '0000-00-00 00:00:00',
		lastedit_date datetime NOT NULL default '0000-00-00 00:00:00',
		ref int(10) NOT NULL default '0',
		lastedit_type varchar(100) NOT NULL default '',
		lastedit_user_id int unsigned NOT NULL default '0',
		to_group_id int(10) unsigned default NULL,
		to_user_id int unsigned default NULL,
		visibility tinyint(1) NOT NULL default '1',
		start_visible datetime NOT NULL default '0000-00-00 00:00:00',
		end_visible datetime NOT NULL default '0000-00-00 00:00:00'
		) TYPE=MyISAM;");

	/*
	-----------------------------------------------------------
		Tool introductions
	-----------------------------------------------------------
	*/
	api_sql_query("
		CREATE TABLE `".$TABLEINTROS."` (
		`id` varchar(50) NOT NULL,
		intro_text text NOT NULL,
		PRIMARY KEY (id))");

	/*
	-----------------------------------------------------------
		Dropbox tool
	-----------------------------------------------------------
	*/
	api_sql_query("
		CREATE TABLE `".$TABLETOOLDROPBOXFILE."` (
		id int unsigned NOT NULL auto_increment,
		uploader_id int unsigned NOT NULL default 0,
		filename varchar(250) NOT NULL default '',
		filesize int unsigned NOT NULL,
		title varchar(250) default '',
		description varchar(250) default '',
		author varchar(250) default '',
		upload_date datetime NOT NULL default '0000-00-00 00:00:00',
		last_upload_date datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY  (id),
		UNIQUE KEY UN_filename (filename)
		)");

	api_sql_query("
		CREATE TABLE `".$TABLETOOLDROPBOXPOST."` (
		file_id int unsigned NOT NULL,
		dest_user_id int unsigned NOT NULL default 0,
		feedback_date datetime NOT NULL default '0000-00-00 00:00:00',
		feedback text default '',
		PRIMARY KEY  (file_id,dest_user_id)
		)");

	api_sql_query("
		CREATE TABLE `".$TABLETOOLDROPBOXPERSON."` (
		file_id int unsigned NOT NULL,
		user_id int unsigned NOT NULL default 0,
		PRIMARY KEY  (file_id,user_id)
		)");

	return 0;
};

/**
*	Fills the course repository with some
*	example content.
*	@version	 1.2
*/
function fill_course_repository($courseRepository)
{
	$sys_course_path = api_get_path(SYS_COURSE_PATH);
	$web_code_path = api_get_path(WEB_CODE_PATH);

	$doc_html = file(api_get_path(SYS_CODE_PATH).'document/example_document.html');

	$fp = fopen($sys_course_path.$courseRepository.'/document/example_document.html', 'w');

	foreach ($doc_html as $key => $enreg)
	{
		$enreg = str_replace('"stones.jpg"', '"'.$web_code_path.'img/stones.jpg"', $enreg);

		fputs($fp, $enreg);
	}

	fclose($fp);
	return 0;
};

/**
 * Function to convert a string from the Dokeos language files to a string ready
 * to insert into the database.
 * @author Bart Mollet (bart.mollet@hogent.be)
 * @param string $string The string to convert
 * @return string The string converted to insert into the database
 */
function lang2db($string)
{
	$string = str_replace("\\'", "'", $string);
	$string = mysql_real_escape_string($string);
	return $string;
}
/**
*	Fills the course database with some required content and example content.
*	@version 1.2
*/
function fill_Db_course($courseDbName, $courseRepository, $language)
{
	global $singleDbEnabled, $courseTablePrefix, $dbGlu, $_user;

	$courseDbName = $courseTablePrefix.$courseDbName.$dbGlu;

	$TABLECOURSEHOMEPAGE = $courseDbName."tool";
	$TABLEINTROS = $courseDbName."tool_intro";

	$TABLEGROUPS = $courseDbName."group_info";
	$TABLEGROUPCATEGORIES = $courseDbName."group_category";
	$TABLEGROUPUSER = $courseDbName."group_rel_user";

	$TABLEITEMPROPERTY = $courseDbName."item_property";

	$TABLETOOLCOURSEDESC = $courseDbName."course_description";
	$TABLETOOLAGENDA = $courseDbName."calendar_event";
	$TABLETOOLANNOUNCEMENTS = $courseDbName."announcement";
	$TABLEADDEDRESOURCES = $courseDbName."resource";
	$TABLETOOLWORKS = $courseDbName."student_publication";
	$TABLETOOLWORKSUSER = $courseDbName."stud_pub_rel_user";
	$TABLETOOLDOCUMENT = $courseDbName."document";
	$TABLETOOLSCORMDOCUMENT = $courseDbName."scormdocument";

	$TABLETOOLLINK = $courseDbName."link";

	$TABLEQUIZ = $courseDbName."quiz";
	$TABLEQUIZQUESTION = $courseDbName."quiz_rel_question";
	$TABLEQUIZQUESTIONLIST = $courseDbName."quiz_question";
	$TABLEQUIZANSWERSLIST = $courseDbName."quiz_answer";

	$TABLEPHPBBACCESS = $courseDbName."bb_access";
	$TABLEPHPBBBANLIST = $courseDbName."bb_banlist";
	$TABLEPHPBBCATEGORIES = $courseDbName."bb_categories";
	$TABLEPHPBBCONFIG = $courseDbName."bb_config";
	$TABLEPHPBBDISALLOW = $courseDbName."bb_disallow";
	$TABLEPHPBBFORUMACCESS = $courseDbName."bb_forum_access";
	$TABLEPHPBBFORUMMODS = $courseDbName."bb_forum_mods";
	$TABLEPHPBBFORUMS = $courseDbName."bb_forums";
	$TABLEPHPBBHEADFOOT = $courseDbName."bb_headermetafooter";
	$TABLEPHPBBDOMAINLIST = $courseDbName."bb_liste_domaines";
	$TABLEPHPBBPAGES = $courseDbName."page";
	$TABLEPHPBBPOSTS = $courseDbName."bb_posts";
	$TABLEPHPBBPOSTSTEXT = $courseDbName."bb_posts_text";
	$TABLEPHPBBPRIVMSG = $courseDbName."bb_priv_msgs";
	$TABLEPHPBBRANK = $courseDbName."bb_ranks";
	$TABLEPHPBBSESSIONS = $courseDbName."bb_sessions";
	$TABLEPHPBBTHEMES = $courseDbName."bb_themes";
	$TABLEPHPBBTOPICS = $courseDbName."bb_topics";
	$TABLEPHPBBUSERS = $courseDbName."bb_users";
	$TABLEPHPBBWHOSONLINE = $courseDbName."bb_whosonline";
	$TABLEPHPBBWORDS = $courseDbName."bb_words";

	$nom = $_user['lastName'];
	$prenom = $_user['firstName'];

	include (api_get_path(SYS_LANG_PATH)."english/create_course.inc.php");
	include (api_get_path(SYS_LANG_PATH).$language."/create_course.inc.php");

	mysql_select_db("$courseDbName");

	api_sql_query("INSERT INTO `".$TABLETOOLDOCUMENT."`(path,title,filetype,size) VALUES ('/example_document.html','example_document.html','file','3367')");
	//we need to add the document properties too!
	$example_doc_id = Database :: get_last_insert_id();
	api_sql_query("INSERT INTO `".$TABLEITEMPROPERTY."` (tool,insert_user_id,insert_date,lastedit_date,ref,lastedit_type,lastedit_user_id,to_group_id,to_user_id,visibility) VALUES ('document',1,NOW(),NOW(),$example_doc_id,'DocumentAdded',1,0,NULL,1)");

	api_sql_query("INSERT INTO `".$TABLEPHPBBACCESS."` VALUES (	'-1',	'Deleted')");
	api_sql_query("INSERT INTO `".$TABLEPHPBBACCESS."` VALUES (	'1',	'User')");
	api_sql_query("INSERT INTO `".$TABLEPHPBBACCESS."` VALUES (	'2',	'Moderator')");
	api_sql_query("INSERT INTO `".$TABLEPHPBBACCESS."` VALUES (	'3',	'Super Moderator')");
	api_sql_query("INSERT INTO `".$TABLEPHPBBACCESS."` VALUES (	'4',	'Administrator')");
	// Create a hidden catagory for group forums
	api_sql_query("INSERT INTO `".$TABLEPHPBBCATEGORIES."` VALUES (1,'".lang2db(get_lang('CatagoryGroup'))."',NULL)");
	// Create an example catagory
	api_sql_query("INSERT INTO `".$TABLEPHPBBCATEGORIES."` VALUES (2,'".lang2db(get_lang('CatagoryMain'))."',NULL)");
	############################## GROUPS ###########################################

	api_sql_query("INSERT INTO `".$TABLEPHPBBCONFIG."` VALUES (
	         '1',
	         '$title',
	         '1',
	         '1',
	         '1',
	         '0',
	         NULL,
	         '1',
	         '15',
	         '15',
	         '50',
	         NULL,
	         '0',
	         '".lang2db(get_lang('Formula'))."',
	         '$email',
	         '$language'
	         )");
	api_sql_query("INSERT INTO `".$TABLEPHPBBFORUMMODS."` VALUES (
	         '1',
	         '1'
	         )");
	api_sql_query("INSERT INTO `".$TABLEPHPBBFORUMS."` VALUES (1,'".lang2db(get_lang('TestForum'))."','".lang2db(get_lang('DelAdmin'))."',2,1,1,1,1,2,0)");
	api_sql_query("INSERT INTO `".$TABLEPHPBBHEADFOOT."` VALUES (
	         '<center><a href=\"../".$courseRepository."\"><img border=0 src=../main/img/logo.gif></a></center>',
	         '',
	         ''
	         )");
	api_sql_query("INSERT INTO `".$TABLEPHPBBPOSTS."` VALUES (1,1,1,-1,NOW(),'127.0.0.1','$nom','$prenom','0','0')");
	api_sql_query("CREATE TABLE `".$TABLEPHPBBPOSTSTEXT."` (
	        post_id int(10) DEFAULT '0' NOT NULL,
	        post_text text,
			post_title varchar(255),
	        PRIMARY KEY (post_id)
	        )");
	api_sql_query("INSERT INTO `".$TABLEPHPBBPOSTSTEXT."` VALUES ('1', '".lang2db(get_lang('Message'))."', '".lang2db(get_lang('ExMessage'))."')");
	// Contenu de la table 'themes'
	api_sql_query("INSERT INTO `".$TABLEPHPBBTHEMES."` VALUES (
	       '1',
	       'Default',
	       '#000000',
	       '#FFFFFF',
	       '#6C706D',
	       '#2E4460',
	       '#001100',
	       'images/header-dark.jpg',
	       'images/new_topic-dark.jpg',
	       'images/reply-dark.jpg',
	       '#0000FF',
	       '#800080',
	       '0',
	       'sans-serif',
	       '1',
	       '2',
	       '-2',
	       '+1',
	       '95%',
	       'images/reply_locked-dark.jpg'
	       )");
	api_sql_query("INSERT INTO `".$TABLEPHPBBTHEMES."` VALUES (
	       '2',
	       'Ocean',
	       '#FFFFFF',
	       '#000000',
	       '#CCCCCC',
	       '#9BB6DA',
	       '#000000',
	       'images/header.jpg',
	       'images/new_topic.jpg',
	       'images/reply.jpg',
	        '#0000FF',
	       '#800080',
	       '0',
	       'sans-serif',
	       '1',
	       '2',
	       '-2',
	       '+1',
	       '95%',
	       'images/reply_locked-dark.jpg'
	       )");
	api_sql_query("INSERT INTO `".$TABLEPHPBBTHEMES."` VALUES (
	        '3',
	        'OCPrices.com',
	        '#FFFFFF',
	        '#000000',
	        '#F5F5F5',
	        '#E6E6E6',
	        '#FFFFFF',
	        'images/forum.jpg',
	        'images/nouveausujet.jpg',
	        'images/repondre.jpg',
	       '#0000FF',
	       '#800080',
	        '1',
	        'Arial,Helvetica, Sans-serif',
	        '1',
	        '2',
	        '-2',
	        '+1',
	        '600',
	        'images/reply_locked-dark.jpg'
	        )");
	// Contenu de la table 'users'
	api_sql_query("INSERT INTO `".$TABLEPHPBBUSERS."` VALUES (
	       '1',
	       '$nom $prenom',
	       NOW(),
	       'password',
	       '$email',
	       NULL,
	       NULL,
	       NULL,
	       NULL,
	       NULL,
	       NULL,
	       NULL,
	       NULL,
	       NULL,
	       NULL,
	       NULL,
	       '0',
	       '0',
	       '0',
	       '0',
	       '0',
	       '0',
	       '1',
	       NULL,
	       NULL,
	       NULL
	       )");
	api_sql_query("INSERT INTO `".$TABLEPHPBBUSERS."` VALUES (
	       '-1',       '".lang2db(get_lang('Anonymous'))."',       NOW(),       'password',       '',
	       NULL,       NULL,       NULL,       NULL,       NULL,       NULL,       NULL,
	       NULL,       NULL,       NULL,       NULL,       '0',       '0',       '0',       '0',       '0',
	       '0',       '1',       NULL,       NULL,       NULL       )");

	/*
	==============================================================================
			All course tables are created.
			Next sections of the script:
			- insert links to all course tools so they can be accessed on the course homepage
			- fill the tool tables with examples
	==============================================================================
	*/

	$visible4all = 1;
	$visible4AdminOfCourse = 0;
	$visible4AdminOfClaroline = 2;

	/*
	-----------------------------------------------------------
		Course homepage tools 
	-----------------------------------------------------------
	*/
	/*
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langCourseDesc)."','course_description/','info.gif','".string2binary(get_setting('course_create_active_tools','course_description'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langAgenda)."','calendar/agenda.php','agenda.gif','".string2binary(get_setting('course_create_active_tools','agenda'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langDoc)."','document/document.php','documents.gif','".string2binary(get_setting('course_create_active_tools','documents'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langScormtool)."','scorm/scormdocument.php','scorm.gif','".string2binary(get_setting('course_create_active_tools','learning_path'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langLinks)."','link/link.php','links.gif','".string2binary(get_setting('course_create_active_tools','links'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langAnnouncements)."','announcements/announcements.php','valves.gif','".string2binary(get_setting('course_create_active_tools','announcements'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langForums)."','phpbb/index.php','forum.gif','".string2binary(get_setting('course_create_active_tools','forums'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langDropbox)."','dropbox/index.php','dropbox.gif','".string2binary(get_setting('course_create_active_tools','dropbox'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langExercices)."','exercice/exercice.php','quiz.gif','".string2binary(get_setting('course_create_active_tools','quiz'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langUsers)."','user/user.php','members.gif','".string2binary(get_setting('course_create_active_tools','users'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langGroups)."','group/group.php','group.gif','".string2binary(get_setting('course_create_active_tools','groups'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langChat)."','chat/chat.php','chat.gif','".string2binary(get_setting('course_create_active_tools','chat'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langOnlineConference)."','online/online.php','conf.gif','".string2binary(get_setting('course_create_active_tools','online_conference'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langWorks)."','work/work.php','works.gif','0','0','squaregrey.gif','NO','_self')");
	*/
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_COURSE_DESCRIPTION."','course_description/','info.gif','".string2binary(get_setting('course_create_active_tools', 'course_description'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_CALENDAR_EVENT."','calendar/agenda.php','agenda.gif','".string2binary(get_setting('course_create_active_tools', 'agenda'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_DOCUMENT."','document/document.php','documents.gif','".string2binary(get_setting('course_create_active_tools', 'documents'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_LEARNPATH."','scorm/scormdocument.php','scorm.gif','".string2binary(get_setting('course_create_active_tools', 'learning_path'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_LINK."','link/link.php','links.gif','".string2binary(get_setting('course_create_active_tools', 'links'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_ANNOUNCEMENT."','announcements/announcements.php','valves.gif','".string2binary(get_setting('course_create_active_tools', 'announcements'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_BB_FORUM."','phpbb/index.php','forum.gif','".string2binary(get_setting('course_create_active_tools', 'forums'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_DROPBOX."','dropbox/index.php','dropbox.gif','".string2binary(get_setting('course_create_active_tools', 'dropbox'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_QUIZ."','exercice/exercice.php','quiz.gif','".string2binary(get_setting('course_create_active_tools', 'quiz'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_USER."','user/user.php','members.gif','".string2binary(get_setting('course_create_active_tools', 'users'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_GROUP."','group/group.php','group.gif','".string2binary(get_setting('course_create_active_tools', 'groups'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_CHAT."','chat/chat.php','chat.gif','".string2binary(get_setting('course_create_active_tools', 'chat'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_CONFERENCE."','online/online.php','conf.gif','".string2binary(get_setting('course_create_active_tools', 'online_conference'))."','0','squaregrey.gif','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_STUDENTPUBLICATION."','work/work.php','works.gif','".string2binary(get_setting('course_create_active_tools', 'student_publications'))."','0','squaregrey.gif','NO','_self')");

	/*
	-----------------------------------------------------------
		Course homepage tools for course admin only
	-----------------------------------------------------------
	*/
	/*
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langStatistics)."','tracking/courseLog.php','statistics.gif','$visible4AdminOfCourse','1','','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langLinkSite)."','link/link.php?action=addlink','npage.gif','$visible4AdminOfCourse','1','','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langModifyInfo)."','course_info/infocours.php','reference.gif','$visible4AdminOfCourse','1','','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langBackup)."','coursecopy/backup.php','backup.gif','$visible4AdminOfCourse','1','','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langCopy)."','coursecopy/copy_course.php','copy.gif','$visible4AdminOfCourse','1','','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".addslashes($langRecycle)."','coursecopy/recycle_course.php','recycle.gif','$visible4AdminOfCourse','1','','NO','_self')");
	*/
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_TRACKING."','tracking/courseLog.php','statistics.gif','$visible4AdminOfCourse','1','','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_HOMEPAGE_LINK."','link/link.php?action=addlink','npage.gif','$visible4AdminOfCourse','1','','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_COURSE_SETTING."','course_info/infocours.php','reference.gif','$visible4AdminOfCourse','1','','NO','_self')");
	//api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_BACKUP."','coursecopy/backup.php','backup.gif','$visible4AdminOfCourse','1','','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_COPY_COURSE_CONTENT."','coursecopy/copy_course.php','copy.gif','$visible4AdminOfCourse','1','','NO','_self')");
	api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_COURSE_RIGHTS_OVERVIEW."','course_info/course_rights.php','reference.gif','$visible4AdminOfCourse','1','','NO','_self')");
	
	//api_sql_query("INSERT INTO `".$TABLECOURSEHOMEPAGE."` VALUES ('','".TOOL_RECYCLE_COURSE."','coursecopy/recycle_course.php','recycle.gif','$visible4AdminOfCourse','1','','NO','_self')");

	/*
	-----------------------------------------------------------
		Course homepage tools for platform admin only
	-----------------------------------------------------------
	*/

	/*
	-----------------------------------------------------------
		Agenda tool
	-----------------------------------------------------------
	*/
	api_sql_query("INSERT INTO `".$TABLETOOLAGENDA."` VALUES ( '', '".lang2db(get_lang('AgendaCreationTitle'))."', '".lang2db(get_lang('AgendaCreationContenu'))."', now(), now())");
	//we need to add the item properties too!
	$insert_id = Database :: get_last_insert_id();
	$sql = "INSERT INTO `".$TABLEITEMPROPERTY."` (tool,insert_user_id,insert_date,lastedit_date,ref,lastedit_type,lastedit_user_id,to_group_id,to_user_id,visibility) VALUES ('".TOOL_CALENDAR_EVENT."',1,NOW(),NOW(),$insert_id,'AgendaAdded',1,0,NULL,1)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Links tool
	-----------------------------------------------------------
	*/
	$add_google_link_sql = "	INSERT INTO `".$TABLETOOLLINK."`
						VALUES ('1','http://www.google.com','Google','".lang2db(get_lang('Google'))."','0','0','0')";
	api_sql_query($add_google_link_sql);
	//we need to add the item properties too!
	$insert_id = Database :: get_last_insert_id();
	$sql = "INSERT INTO `".$TABLEITEMPROPERTY."` (tool,insert_user_id,insert_date,lastedit_date,ref,lastedit_type,lastedit_user_id,to_group_id,to_user_id,visibility) VALUES ('".TOOL_LINK."',1,NOW(),NOW(),$insert_id,'LinkAdded',1,0,NULL,1)";
	api_sql_query($sql, __FILE__, __LINE__);

	$add_wikipedia_link_sql = "	INSERT INTO `".$TABLETOOLLINK."`
						VALUES ('','http://www.wikipedia.org','Wikipedia','".lang2db(get_lang('Wikipedia'))."','0','1','0')";
	api_sql_query($add_wikipedia_link_sql);
	//we need to add the item properties too!
	$insert_id = Database :: get_last_insert_id();
	$sql = "INSERT INTO `".$TABLEITEMPROPERTY."` (tool,insert_user_id,insert_date,lastedit_date,ref,lastedit_type,lastedit_user_id,to_group_id,to_user_id,visibility) VALUES ('".TOOL_LINK."',1,NOW(),NOW(),$insert_id,'LinkAdded',1,0,NULL,1)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Annoucement tool
	-----------------------------------------------------------
	*/
	$sql = "INSERT INTO `".$TABLETOOLANNOUNCEMENTS."` VALUES ( '','".lang2db(get_lang('AnnouncementExampleTitle'))."', '".lang2db(get_lang('AnnouncementEx'))."', NOW(), '1')";
	api_sql_query($sql, __FILE__, __LINE__);
	//we need to add the item properties too!
	$insert_id = Database :: get_last_insert_id();
	$sql = "INSERT INTO `".$TABLEITEMPROPERTY."` (tool,insert_user_id,insert_date,lastedit_date,ref,lastedit_type,lastedit_user_id,to_group_id,to_user_id,visibility) VALUES ('".TOOL_ANNOUNCEMENT."',1,NOW(),NOW(),$insert_id,'AnnouncementAdded',1,0,NULL,1)";
	api_sql_query($sql, __FILE__, __LINE__);

	/*
	-----------------------------------------------------------
		Introduction text
	-----------------------------------------------------------
	*/
	api_sql_query("INSERT INTO `".$TABLEINTROS."` VALUES ('".TOOL_COURSE_HOMEPAGE."','".lang2db(get_lang('IntroductionText'))."')");
	api_sql_query("INSERT INTO `".$TABLEINTROS."` VALUES ('".TOOL_STUDENTPUBLICATION."','".lang2db(get_lang('IntroductionTwo'))."')");

	/*
	-----------------------------------------------------------
		Exercise tool
	-----------------------------------------------------------
	*/
	api_sql_query("INSERT INTO `".$TABLEQUIZANSWERSLIST."` VALUES ( '1', '1', '".lang2db(get_lang('Ridiculise'))."', '0', '".lang2db(get_lang('NoPsychology'))."', '-5', '1')",__FILE__,__LINE__);
	api_sql_query("INSERT INTO `".$TABLEQUIZANSWERSLIST."` VALUES ( '2', '1', '".lang2db(get_lang('AdmitError'))."', '0', '".lang2db(get_lang('NoSeduction'))."', '-5', '2')");
	api_sql_query("INSERT INTO `".$TABLEQUIZANSWERSLIST."` VALUES ( '3', '1', '".lang2db(get_lang('Force'))."', '1', '".lang2db(get_lang('Indeed'))."', '5', '3')");
	api_sql_query("INSERT INTO `".$TABLEQUIZANSWERSLIST."` VALUES ( '4', '1', '".lang2db(get_lang('Contradiction'))."', '1', '".lang2db(get_lang('NotFalse'))."', '5', '4')");
	api_sql_query("INSERT INTO `".$TABLEQUIZ."` VALUES ( '1', '".lang2db(get_lang('ExerciceEx'))."', '".lang2db(get_lang('Antique'))."', '', '1', '0', '1')");
	api_sql_query("INSERT INTO `".$TABLEQUIZQUESTIONLIST."` VALUES ( '1', '".lang2db(get_lang('SocraticIrony'))."', '".lang2db(get_lang('ManyAnswers'))."', '10', '1', '2','')");
	api_sql_query("INSERT INTO `".$TABLEQUIZQUESTION."` VALUES ( '1', '1')");

	/*
	-----------------------------------------------------------
		Forum tool
	-----------------------------------------------------------
	*/
	api_sql_query("INSERT INTO `".$TABLEPHPBBTOPICS."` VALUES (1,'".lang2db(get_lang('ExMessage'))."',-1,'2001-09-18 20:25',1,'',1,1,'0','1', '$nom', '$prenom')");

	/*
	-----------------------------------------------------------
		Group tool
	-----------------------------------------------------------
	*/
	api_sql_query("INSERT INTO `".$TABLEGROUPCATEGORIES."` ( id , title , description , forum_state , max_student , self_reg_allowed , self_unreg_allowed , groups_per_user , display_order ) VALUES ('2', '".lang2db(get_lang('DefaultGroupCategory'))."', '', '1', '8', '0', '0', '0', '0');");

	return 0;
};

/**
 * function string2binary converts the string "true" or "false" to the boolean true false (0 or 1)
 * This is used for the Dokeos Config Settings as these store true or false as string
 * and the get_setting('course_create_active_tools') should be 0 or 1 (used for
 * the visibility of the tool)
 * @param string	$variable
 * @author Patrick Cool, patrick.cool@ugent.be
 */
function string2binary($variable)
{
	if ($variable == "true")
	{
		return true;
	}
	if ($variable == "false")
	{
		return false;
	}
}

/**
 * function register_course to create a record in the course table of the main database
 * @param string	$courseId
 * @param string	$courseCode
 * @param string	$courseRepository
 * @param string	$courseDbName
 * @param string	$tutor_name
 * @param string	$category
 * @param string	$title			complete name of course
 * @param string	$course_language		lang for this course
 * @param string	$uid				uid of owner
 */
function register_course($courseSysCode, $courseScreenCode, $courseRepository, $courseDbName, $titular, $category, $title, $course_language, $uidCreator, $expiration_date = "")
{
	GLOBAL $defaultVisibilityForANewCourse, $langCourseDescription, $langProfessor, $langAnnouncementEx, $error_msg, $courseTablePrefix, $dbGlu;
	$TABLECOURSE = Database :: get_main_table(MAIN_COURSE_TABLE);
	$TABLECOURSUSER = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
	$location_table = Database::get_main_table(MAIN_LOCATION_TABLE);
	$user_role_table = Database::get_main_table(MAIN_USER_ROLE_TABLE);

	#$TABLEANNOUNCEMENTS=$courseTablePrefix.$courseDbName.$dbGlu.$TABLEANNOUNCEMENTS;
	$TABLEANNOUNCEMENTS = Database :: get_course_announcement_table($courseDbName);

	$okForRegisterCourse = true;

	// Check if  I have all
	if (empty ($courseSysCode))
	{
		$error_msg[] = "courseSysCode is missing";
		$okForRegisterCourse = false;
	}
	if (empty ($courseScreenCode))
	{
		$error_msg[] = "courseScreenCode is missing";
		$okForRegisterCourse = false;
	}
	if (empty ($courseDbName))
	{
		$error_msg[] = "courseDbName is missing";
		$okForRegisterCourse = false;
	}
	if (empty ($courseRepository))
	{
		$error_msg[] = "courseRepository is missing";
		$okForRegisterCourse = false;
	}
	if (empty ($titular))
	{
		$error_msg[] = "titular is missing";
		$okForRegisterCourse = false;
	}
	if (empty ($title))
	{
		$error_msg[] = "title is missing";
		$okForRegisterCourse = false;
	}
	if (empty ($course_language))
	{
		$error_msg[] = "language is missing";
		$okForRegisterCourse = false;
	}
	if (empty ($uidCreator))
	{
		$error_msg[] = "uidCreator is missing";
		$okForRegisterCourse = false;
	}

	if (empty ($expiration_date))
	{
		$expiration_date = "NULL";
	}
	else
	{
		$expiration_date = "FROM_UNIXTIME(".$expiration_date.")";
	}

	if ($okForRegisterCourse)
	{
		// here we must add 2 fields
		$sql = "INSERT INTO ".$TABLECOURSE." SET
					code = '".$courseSysCode."',
					db_name = '".$courseDbName."',
					directory = '".$courseRepository."',
					course_language = '".$course_language."',
					title = '".$title."',
					description = '".lang2db($langCourseDescription)."',
					category_code = '".$category."',
					visibility = '".$defaultVisibilityForANewCourse."',
					show_score = '',
					disk_quota = '".get_setting('default_document_quotum')."',
					creation_date = now(),
					expiration_date = ".$expiration_date.",
					last_edit = now(),
					last_visit = NULL,
					tutor_name = '".$titular."',
					visual_code = '".$courseScreenCode."'";
		api_sql_query($sql, __FILE__, __LINE__);

		$sort = api_max_sort_value('0', $uidCreator);

		$sql = "INSERT INTO ".$TABLECOURSUSER." SET
					course_code = '".$courseSysCode."',
					user_id = '".$uidCreator."',
					status = '1',
					role = '".lang2db('Professor')."',
					tutor_id='1',
					sort='". ($sort +1)."',
					user_course_cat='0'";
		api_sql_query($sql, __FILE__, __LINE__);

		//add an entry into the user_role table
		$role_id = 9 ; //course admin
		$location = "platform|courses|course,$courseSysCode";
		$find_location_id_sql = "SELECT id FROM $location_table WHERE location='$location'";
		$sql_result = api_sql_query($find_location_id_sql, __FILE__, __LINE__);
		$result = mysql_fetch_array($sql_result);
		$location_id = $result['id'];
		$set_role_sql = "INSERT INTO $user_role_table SET user_id='$uidCreator', role_id='$role_id', location_id='$location_id'";
		api_sql_query($set_role_sql, __FILE__, __LINE__);
	}

	return 0;
}

/**
*	WARNING: this function always returns true.
*/
function checkArchive($pathToArchive)
{
	return TRUE;
}

function readPropertiesInArchive($archive, $isCompressed = TRUE)
{
	include (api_get_library_path()."/pclzip/pclzip.lib.php");
	printVar(dirname($archive), "Zip : ");
	/*
	string tempnam ( string dir, string prefix)
	tempnam() cr� un fichier temporaire unique dans le dossier dir. Si le dossier n'existe pas, tempnam() va g��er un nom de fichier dans le dossier temporaire du syst�e.
	Avant PHP 4.0.6, le comportement de tempnam() d�endait de l'OS sous-jacent. Sous Windows, la variable d'environnement TMP remplace le param�re dir; sous Linux, la variable d'environnement TMPDIR a la priorit� tandis que pour les OS en syst�e V R4, le param�re dir sera toujours utilis� si le dossier qu'il repr�ente existe. Consultez votre documentation pour plus de d�ails.
	tempnam() retourne le nom du fichier temporaire, ou la cha�e NULL en cas d'�hec.
	*/
	$zipFile = new pclZip($archive);
	$tmpDirName = dirname($archive)."/tmp".$uid.uniqid($uid);
	if (mkpath($tmpDirName))
		$unzippingSate = $zipFile->extract($tmpDirName);
	else
		die("mkpath va pas");
	$pathToArchiveIni = dirname($tmpDirName)."/archive.ini";
	//	echo $pathToArchiveIni;
	$courseProperties = parse_ini_file($pathToArchiveIni);
	rmdir($tmpDirName);
	return $courseProperties;
}
?>