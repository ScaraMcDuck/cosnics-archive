<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Evie, Free University of Brussels (Belgium)
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
*	Install the Dokeos database
*	Notice : This script has to be included by index.php
*
*	@package dokeos.install
==============================================================================
*/
require_once dirname(__FILE__).'/../../users/lib/user.class.php';
require_once dirname(__FILE__).'/../../users/lib/usersdatamanager.class.php';
require_once dirname(__FILE__).'/../../application/lib/weblcms/weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../application/lib/weblcms/course/coursecategory.class.php';
/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/**
* Create all default databases and their tables.
*/
function full_database_install($values)
{
	set_file_folder_permissions(); //what's this doing here?
	connect_to_database_server($values['database_host'], $values['database_username'], $values['database_password']);
	create_databases($values);
	echo "<p>Database creation is complete!</p>";
}

/**
* Connects to the database server. Currently this is always MySQL.
* @todo convert to Pear MDB2.
*/
function connect_to_database_server($database_host,$database_username,$database_password)
{
	@mysql_connect($database_host,$database_username,$database_password);

	if(mysql_errno() > 0)
	{
		$no=mysql_errno();
		$msg=mysql_error();

		echo '<hr />['.$no.'] &ndash; '.$msg.'<hr>
		The MySQL server doesn\'t work or login / pass is bad.<br /><br />
		Please check these values:<br /><br />
		<b>host</b> : '.$database_host.'<br />
		<b>user</b> : '.$database_username.'<br />
		<b>password</b> : '.$database_password.'<br /><br />
		Please go back to step 3.
		<p><input type="submit" name="step3" value="&lt; Back" /></p>
		</td></tr></table></form></body></html>';

		exit();
	}
}

/**
* Creates the default databases.
*/
function create_databases($values)
{
	$databases = Page_DatabaseSettings::get_databases();
	$databases_to_create = array();
	if($values['database_single'])
	{
		$databases_to_create[] = 'database_main_db';
	}
	else
	{
		$databases_to_create = array_keys($databases);
	}
	foreach($databases_to_create as $index => $database)
	{
		//TODO Database abstraction!
		mysql_query('DROP DATABASE IF EXISTS '.$values[$database]) or die('DROP ERROR '.mysql_error());
		mysql_query('CREATE DATABASE IF NOT EXISTS '.$values[$database]) or die('CREATE ERROR '.mysql_error());
	}
	create_main_database_tables($values);
	create_tracking_database_tables($values);
	create_scorm_database_tables($values);
	create_user_database_tables($values);
}

/**
* creating the tables of the main database
*/
function create_main_database_tables($values)
{
	//TODO: Database abstraction
	mysql_select_db($values['database_main_db']) or die('SELECT DB ERROR '.mysql_error());

	$installation_settings['{ORGANISATIONNAME}'] = $values['organization_name'];
	$installation_settings['{ORGANISATIONURL}'] = $values['organization_url'];
	$installation_settings['{CAMPUSNAME}'] = $values['platform_name'];
	$installation_settings['{PLATFORMLANGUAGE}'] = $values['platform_language'];
	$installation_settings['{ALLOWSELFREGISTRATION}'] = boolean_to_string($values['self_reg']);
	$installation_settings['{ALLOWTEACHERSELFREGISTRATION}'] = boolean_to_string($values['self_reg_teacher']);
	$installation_settings['{ADMINLASTNAME}'] = $values['admin_lastname'];
	$installation_settings['{ADMINFIRSTNAME}'] = $values['admin_firstname'];
	$installation_settings['{ADMINLOGIN}'] = $values['admin_username'];
	$installation_settings['{ADMINPASSWORD}'] = $values['admin_password'];
	$installation_settings['{ADMINEMAIL}'] = $values['admin_email'];
	$installation_settings['{ADMINPHONE}'] = $values['admin_phone'];
	$installation_settings['{PLATFORM_AUTH_SOURCE}'] = PLATFORM_AUTH_SOURCE;
	load_main_database($installation_settings);
}

function create_default_categories_in_weblcms()
{
	$wdm = WeblcmsDataManager :: get_instance();

	//Creating language skills
	$cat = new CourseCategory();
	$cat->set_name('Language skills');
	$cat->set_code('LANG');
	$cat->set_parent('0');
	$cat->set_tree_pos('1');
	$cat->set_children_count('0');
	$cat->set_auth_course_child('1');
	$cat->set_auth_cat_child('1');
	$cat->create();

	//creating pc skills
	$cat = new CourseCategory();
	$cat->set_name('PC skills');
	$cat->set_code('PC');
	$cat->set_parent('0');
	$cat->set_tree_pos('2');
	$cat->set_children_count('0');
	$cat->set_auth_course_child('1');
	$cat->set_auth_cat_child('1');
	$cat->create();

	//creating Projects
	$cat = new CourseCategory();
	$cat->set_name('Projects');
	$cat->set_code('PROJ');
	$cat->set_parent('0');
	$cat->set_tree_pos('3');
	$cat->set_children_count('0');
	$cat->set_auth_course_child('1');
	$cat->set_auth_cat_child('1');
	$cat->create();

}

function create_admin_in_user_table($values)
{
	$user = new User();
	$user->set_lastname($values['admin_lastname']);
	$user->set_firstname($values['admin_firstname']);
	$user->set_username($values['admin_username']);
	$user->set_password(md5($values['admin_password']));
	$user->set_auth_source(PLATFORM_AUTH_SOURCE);
	$user->set_email($values['admin_email']);
	$user->set_status('1');
	$user->set_platformadmin('1');
	$user->set_official_code('ADMIN');
	$user->set_phone($values['admin_phone']);
	$user->set_language($values['install_language']);
	$user->set_disk_quota('209715200');
	$user->set_database_quota('300');
	$user->set_version_quota('20');
	$user->create();
}

/**
* creating the tables of the tracking database
*/
function create_tracking_database_tables($values)
{
	mysql_select_db($values['database_tracking']) or die('SELECT DB ERROR '.mysql_error());

	mysql_query("CREATE TABLE `track_c_browsers` (
				`id` int(11) NOT NULL auto_increment,
				`browser` varchar(255) NOT NULL default '',
				`counter` int(11) NOT NULL default '0',
				PRIMARY KEY  (`id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_c_countries` (
				`id` int(11) NOT NULL auto_increment,
				`code` varchar(40) NOT NULL default '',
				`country` varchar(50) NOT NULL default '',
				`counter` int(11) NOT NULL default '0',
				PRIMARY KEY  (`id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_c_os` (
				`id` int(11) NOT NULL auto_increment,
				`os` varchar(255) NOT NULL default '',
				`counter` int(11) NOT NULL default '0',
				PRIMARY KEY  (`id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_c_providers` (
				`id` int(11) NOT NULL auto_increment,
				`provider` varchar(255) NOT NULL default '',
				`counter` int(11) NOT NULL default '0',
				PRIMARY KEY  (`id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_c_referers` (
				`id` int(11) NOT NULL auto_increment,
				`referer` varchar(255) NOT NULL default '',
				`counter` int(11) NOT NULL default '0',
				PRIMARY KEY  (`id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_access` (
				`access_id` int(11) NOT NULL auto_increment,
				`access_user_id` int unsigned default NULL,
				`access_date` datetime NOT NULL default '0000-00-00 00:00:00',
				`access_cours_code` varchar(40) NOT NULL default '0',
				`access_tool` varchar(30) default NULL,
				PRIMARY KEY  (`access_id`),
				KEY `access_user_id` (`access_user_id`),
				KEY `access_cours_code` (`access_cours_code`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_lastaccess` (
				`access_id` bigint(20) NOT NULL auto_increment,
				`access_user_id` int unsigned default NULL,
				`access_date` datetime NOT NULL default '0000-00-00 00:00:00',
				`access_cours_code` varchar(40) NOT NULL default '0',
				`access_tool` varchar(30) default NULL,
				PRIMARY KEY  (`access_id`),
				KEY `access_user_id` (`access_user_id`),
				KEY `access_cours_code` (`access_cours_code`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_default` (
				`default_id` int(11) NOT NULL auto_increment,
				`default_user_id` int unsigned NOT NULL default '0',
				`default_cours_code` varchar(40) NOT NULL default '',
				`default_date` datetime NOT NULL default '0000-00-00 00:00:00',
				`default_event_type` varchar(20) NOT NULL default '',
				`default_value_type` varchar(20) NOT NULL default '',
				`default_value` tinytext NOT NULL,
				PRIMARY KEY  (`default_id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_downloads` (
				`down_id` int(11) NOT NULL auto_increment,
				`down_user_id` int unsigned default NULL,
				`down_date` datetime NOT NULL default '0000-00-00 00:00:00',
				`down_cours_id` varchar(20) NOT NULL default '0',
				`down_doc_path` varchar(255) NOT NULL default '0',
				PRIMARY KEY  (`down_id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_exercices` (
				`exe_id` int(11) NOT NULL auto_increment,
				`exe_user_id` int unsigned default NULL,
				`exe_date` datetime NOT NULL default '0000-00-00 00:00:00',
				`exe_cours_id` varchar(40) NOT NULL default '',
				`exe_exo_id` mediumint unsigned NOT NULL default '0',
				`exe_result` smallint NOT NULL default '0',
				`exe_weighting` smallint NOT NULL default '0',
				PRIMARY KEY  (`exe_id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_hotpotatoes` (
				`exe_name` VARCHAR( 255 ) NOT NULL ,
				`exe_user_id` int unsigned DEFAULT NULL ,
				`exe_date` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL ,
				`exe_cours_id` varchar(40) NOT NULL ,
				`exe_result` smallint DEFAULT '0' NOT NULL ,
				`exe_weighting` smallint DEFAULT '0' NOT NULL
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_links` (
				`links_id` int NOT NULL auto_increment,
				`links_user_id` int unsigned default NULL,
				`links_date` datetime NOT NULL default '0000-00-00 00:00:00',
				`links_cours_id` varchar(20) NOT NULL default '0',
				`links_link_id` int(11) NOT NULL default '0',
				PRIMARY KEY  (`links_id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_login` (
				`login_id` int NOT NULL auto_increment,
				`login_user_id` int unsigned NOT NULL default '0',
				`login_date` datetime NOT NULL default '0000-00-00 00:00:00',
				`login_ip` varchar(39) NOT NULL default '',
				PRIMARY KEY  (`login_id`),
				KEY `login_user_id` (`login_user_id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_online` (
				`login_id` int NOT NULL auto_increment,
				`login_user_id` int unsigned NOT NULL default '0',
				`login_date` datetime NOT NULL default '0000-00-00 00:00:00',
				`login_ip` varchar(39) NOT NULL default '',
				`course` varchar(40) default NULL,
				PRIMARY KEY  (`login_id`),
				KEY `login_user_id` (`login_user_id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_open` (
				`open_id` int(11) NOT NULL auto_increment,
				`open_remote_host` tinytext NOT NULL,
				`open_agent` tinytext NOT NULL,
				`open_referer` tinytext NOT NULL,
				`open_date` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY  (`open_id`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `track_e_uploads` (
				`upload_id` int NOT NULL auto_increment,
				`upload_user_id` int unsigned default NULL,
				`upload_date` datetime NOT NULL default '0000-00-00 00:00:00',
				`upload_cours_id` varchar(20) NOT NULL default '0',
				`upload_work_id` int(11) NOT NULL default '0',
				PRIMARY KEY  (`upload_id`)
				) TYPE=MyISAM") or die(mysql_error());

	$track_countries_table = "track_c_countries";
	fill_track_countries_table($track_countries_table);
}

/**
* creating the tables of the SCORM database
*/
function create_scorm_database_tables($values)
{
	mysql_select_db($values['database_scorm']) or die(mysql_error());

	mysql_query("CREATE TABLE `scorm_main` (
				`contentId` int(5) unsigned NOT NULL auto_increment,
				`contentTitle` varchar(100) default NULL,
				`dokeosCourse` varchar(100) default NULL,
				PRIMARY KEY  (`contentId`)
				) TYPE=MyISAM") or die(mysql_error());

	mysql_query("CREATE TABLE `scorm_sco_data` (
				`contentId` int(5) default NULL,
				`scoId` int(5) NOT NULL auto_increment,
				`scoIdentifier` varchar(100) default NULL,
				`scoTitle` varchar(100) default NULL,
				`status` varchar(100) default NULL,
				`studentId` int(10) default NULL,
				`score` int(10) default NULL,
				`time` varchar(20) default NULL,
				KEY `scoId` (`scoId`)
				) TYPE=MyISAM") or die(mysql_error());
}

/**
* creating the tables of the USER database
* this database stores
* - the personal agenda items are stored
* - the user defined course categories (sorting of my courses)
*/
function create_user_database_tables($values)
{
	mysql_select_db($values['database_user']) or die(mysql_error());

	// creating the table where the personal agenda items are stored
	mysql_query("CREATE TABLE `personal_agenda` (
				`id` int NOT NULL auto_increment,
				`user` int unsigned,
				`title` text,
				`text` text,
				`date` datetime default NULL,
				`enddate` datetime default NULL,
				`course` varchar(255),
				UNIQUE KEY `id` (`id`))
				TYPE=MyISAM") or die(mysql_error());

	// creating the table that is used for the user defined course categories
	mysql_query("CREATE TABLE `user_course_category` (
				`id` int unsigned NOT NULL auto_increment,
				`user_id` int unsigned NOT NULL default '0',
				`title` text NOT NULL,
				PRIMARY KEY  (`id`)
				) TYPE=MyISAM") or die(mysql_error());
}

/*
==============================================================================
		MAIN CODE
==============================================================================
*/

//this page can only be access through including from the install script.

if( ! defined('DOKEOS_INSTALL'))
{
	echo 'You are not allowed here!';
	exit;
}
?>