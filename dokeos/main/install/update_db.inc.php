<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
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
* Update the Dokeos database from an older version
* Notice : This script has to be included by index.php and update_courses.php
*
* @package dokeos.install
* @todo
* - conditional changing of tables. Currently we execute for example
* ALTER TABLE `$dbNameForm`.`cours` instructions without checking wether this is necessary.
* - reorganise code into functions
==============================================================================
*/

require_once ("install_upgrade.lib.php");

//basic rights
define('VIEW_RIGHT', 1);
define('EDIT_RIGHT', 2);
define('ADD_RIGHT', 3);
define('DELETE_RIGHT', 4);

//basic roles needed when upgrading
define('ANONYMOUS_GUEST_COURSE_VISITOR', 5);
define('REGISTERED_GUEST_COURSE_VISITOR', 6);
define('NORMAL_COURSE_MEMBER', 7);
define('TEACHING_ASSISTANT', 8);
define('COURSE_ADMIN', 9);

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/**
* Function used to upgrade from 1.6.x versions to 1.7.
*/
function upgrade_v16_to_v17($main_database, $statistic_database)
{
	//1. dokeos_main.sys_announcement : visibility fields changed from enum('true','false') to enum('0','1')
	mysql_query("ALTER TABLE `$main_database`.`sys_announcement` CHANGE `visible_teacher` `visible_teacher` enum('0','1') NOT NULL default '0'");
	mysql_query("ALTER TABLE `$main_database`.`sys_announcement` CHANGE `visible_student` `visible_student` enum('0','1') NOT NULL default '0'");
	mysql_query("ALTER TABLE `$main_database`.`sys_announcement` CHANGE `visible_guest` `visible_guest` enum('0','1') NOT NULL default '0'");

	//2.1. default auth_source renamed from 'claroline' to 'platform'. Use the PLATFORM_AUTH_SOURCE constant defined in main_api.lib.php 
	mysql_query("UPDATE `$main_database`.`user` SET `auth_source`='".PLATFORM_AUTH_SOURCE."' WHERE `auth_source`='claroline'");
	mysql_query("UPDATE `$main_database`.`user` SET `auth_source`='".PLATFORM_AUTH_SOURCE."' WHERE `auth_source`='dokeos'");

	//2.2. add language-field to user database
	mysql_query("ALTER TABLE `$main_database`.`user` ADD `language` varchar(40) default NULL");

	//3. change table for who is online
	mysql_query("ALTER TABLE `$statistic_database`.`track_e_online` ADD `course` varchar(40) default NULL");

	//4. conditionally add new languages, settings...
	mysql_select_db($main_database);
	add_new_settings_v17();

	//5. add roles and rights tables with default content
	mysql_select_db($main_database);
	add_roles_rights_tables_v17();

	//6. fix for converting the old course homepage links to using the links tool
	convert_homepage_links_v17();

	//7. for all existing courses: apply default roles/rights permissions
	mysql_select_db($main_database);
	apply_roles_rights_defaults_to_existing_courses_v17();
	update_existing_visibility_settings_to_roles_rights_v17();
	assign_local_roles_for_enrolled_users_v17();

	//8. fix personal courses sorting
	mysql_select_db($main_database);
	fix_personal_course_sorting_v17();

	//9. new role-right course module: record must be added to dokeos_main.course_module
	//and new tool entry added to every course
	add_roles_rights_overview_tool_to_existing_courses_v17();

	//10. Replace the old image names by the new names
	update_image_names_v17();
	
	//11. Make sure DB-field for group docs is same size as path field in document table
	update_secret_directory_field_v17();
}

/**
* Existing courses should have a value entered for the course_rel_user.sort field.
* WARNING this code is not very good
* - it gives every course_user entry a sort field value,
* but it ignores existing values...
*
* In Dokeos versions before 1.6, there was no sort field in the course_rel_user table,
* this has appeared in Dokeos 1.6. The upgrade from 1.5 to 1.6.0 did not take this into
* account yet, later 1.6.x versions did.
*/
function fix_personal_course_sorting_v17()
{
	$user_table = "`user`";
	$course_user_table = "`course_rel_user`";

	$sql_users = "SELECT * FROM $user_table";
	$result_users = mysql_query($sql_users);
	while ($this_user = mysql_fetch_array($result_users))
	{
		$counter = 1;
		$sql_course_user = "SELECT * FROM $course_user_table WHERE user_id='".$this_user['user_id']."'";
		$result_course_user = mysql_query($sql_course_user);
		while ($this_course_user = mysql_fetch_array($result_course_user))
		{
			$update = "UPDATE $course_user_table SET sort='$counter' WHERE user_id='".$this_user['user_id']."' AND course_code='".$this_course_user['course_code']."'";
			$result_update = mysql_query($update);
			$counter ++;
		}
	}
}

/**
* In Dokeos versions before 1.6, links on the homepage were stored in the
* tool table, since 1.6 they are stored as a link in the link tool with an 
* "on homepage" option. The upgrade from 1.5 to 1.6.0 did not take this into
* account yet, later 1.6.x versions did.
*/
function convert_homepage_links_v17()
{
	global $dbGlu;
	//select all courses, including virtual ones
	$sql_result = mysql_query("SELECT * FROM `course`");
	while ($course_result = mysql_fetch_array($sql_result))
	{
		$course_database = $course_result['db_name'];
		$tool_table = "`".$course_database.$dbGlu."tool`";
		$link_table = "`".$course_database.$dbGlu."link`";
		$item_property_table = "`".$course_database.$dbGlu."item_property`";

		// step 1: count the max display order of the 0 category_id
		$sql = "SELECT * FROM $link_table WHERE category_id='0' ORDER BY display_order DESC";
		$result2 = mysql_query($sql);
		$row = mysql_fetch_array($result2);
		$maxsort = $row['display_order'];

		// step 2: select all the links that were added to the course homepage
		$sql = "SELECT * FROM $tool_table WHERE link LIKE 'http://%'";
		$result2 = mysql_query($sql);
		while ($row = mysql_fetch_array($result2))
		{
			$maxsort ++;
			// step 3: for each link on homepage: add to the links table
			$sql_insert = "INSERT INTO $link_table (url, title, category_id, display_order, on_homepage) VALUES('".$row['link']."','".$row['name']."','0','".$maxsort."','1')";
			$result_insert = mysql_query($sql_insert);
			$insert_id = mysql_insert_id();

			// step 4: for each link on homepage: add the link in the item_property table
			$sql_item_property = "INSERT INTO $item_property_table SET ";
			$sql_item_property .= " tool = '".TOOL_LINK."', ";
			$sql_item_property .= " ref = '".$insert_id."', ";
			$sql_item_property .= " lastedit_type = 'LinkAdded', ";
			$sql_item_property .= " to_group_id = '0' ";
			mysql_query($sql_item_property);

			// step 5: for each link on homepage: delete the link in the tool table.				
			$sql_delete = "DELETE FROM $tool_table WHERE id='".$row['id']."'";
			$resultdelete = mysql_query($sql_delete);
		}
	}
}

/**
* During 1.6.x development, some changes were made to the database.
* We have to take these into account. E.g. Esperanto was not available yet in 1.6.0
* but was made available in 1.6.1.
*/
function add_new_settings_v17()
{
	//when trying to insert existing rows (languages), mysql should just fail
	//and leave the table untouched
	@ mysql_query("INSERT INTO `language` VALUES (34, 'Esperanto', 'esperanto', 'es', 'esperanto', 1);");

	//remove old show online setting, this will be replaced by three new settings
	mysql_query("DELETE FROM `settings_current` WHERE `id` = '14'");

	//renumber existing settings
	//last to first, otherwise renumbering won't be accepted
	//because of duplicate keys
	mysql_query("UPDATE `settings_current` SET `id`='59' WHERE `id`='55'");
	mysql_query("UPDATE `settings_current` SET `id`='58' WHERE `id`='54'");
	mysql_query("UPDATE `settings_current` SET `id`='57' WHERE `id`='53'");
	mysql_query("UPDATE `settings_current` SET `id`='56' WHERE `id`='52'");
	mysql_query("UPDATE `settings_current` SET `id`='55' WHERE `id`='51'");
	mysql_query("UPDATE `settings_current` SET `id`='54' WHERE `id`='50'");
	mysql_query("UPDATE `settings_current` SET `id`='53' WHERE `id`='49'");
	mysql_query("UPDATE `settings_current` SET `id`='52' WHERE `id`='48'");
	mysql_query("UPDATE `settings_current` SET `id`='51' WHERE `id`='47'");
	mysql_query("UPDATE `settings_current` SET `id`='50' WHERE `id`='46'");
	mysql_query("UPDATE `settings_current` SET `id`='49' WHERE `id`='45'");
	mysql_query("UPDATE `settings_current` SET `id`='48' WHERE `id`='44'");
	mysql_query("UPDATE `settings_current` SET `id`='47' WHERE `id`='43'");
	//course_create_active_tools - old: 29-42:, mapped to new 33-46
	mysql_query("UPDATE `settings_current` SET `id`='46' WHERE `id`='42'");
	mysql_query("UPDATE `settings_current` SET `id`='45' WHERE `id`='41'");
	mysql_query("UPDATE `settings_current` SET `id`='44' WHERE `id`='40'");
	mysql_query("UPDATE `settings_current` SET `id`='43' WHERE `id`='39'");
	mysql_query("UPDATE `settings_current` SET `id`='42' WHERE `id`='38'");
	mysql_query("UPDATE `settings_current` SET `id`='41' WHERE `id`='37'");
	mysql_query("UPDATE `settings_current` SET `id`='40' WHERE `id`='36'");
	mysql_query("UPDATE `settings_current` SET `id`='39' WHERE `id`='35'");
	mysql_query("UPDATE `settings_current` SET `id`='38' WHERE `id`='34'");
	mysql_query("UPDATE `settings_current` SET `id`='37' WHERE `id`='33'");
	mysql_query("UPDATE `settings_current` SET `id`='36' WHERE `id`='32'");
	mysql_query("UPDATE `settings_current` SET `id`='35' WHERE `id`='31'");
	mysql_query("UPDATE `settings_current` SET `id`='34' WHERE `id`='30'");
	mysql_query("UPDATE `settings_current` SET `id`='33' WHERE `id`='29'");
	//other
	mysql_query("UPDATE `settings_current` SET `id`='32' WHERE `id`='28'");
	mysql_query("UPDATE `settings_current` SET `id`='31' WHERE `id`='27'");
	mysql_query("UPDATE `settings_current` SET `id`='30' WHERE `id`='26'");
	mysql_query("UPDATE `settings_current` SET `id`='29' WHERE `id`='25'");
	mysql_query("UPDATE `settings_current` SET `id`='28' WHERE `id`='24'");
	mysql_query("UPDATE `settings_current` SET `id`='26' WHERE `id`='23'");
	mysql_query("UPDATE `settings_current` SET `id`='25' WHERE `id`='22'");
	mysql_query("UPDATE `settings_current` SET `id`='24' WHERE `id`='21'");
	mysql_query("UPDATE `settings_current` SET `id`='22' WHERE `id`='20'");
	mysql_query("UPDATE `settings_current` SET `id`='21' WHERE `id`='19'");
	mysql_query("UPDATE `settings_current` SET `id`='20' WHERE `id`='18'");
	mysql_query("UPDATE `settings_current` SET `id`='19' WHERE `id`='17'");
	mysql_query("UPDATE `settings_current` SET `id`='18' WHERE `id`='16'");
	mysql_query("UPDATE `settings_current` SET `id`='17' WHERE `id`='15'");

	//add the new settings
	mysql_query("INSERT INTO `settings_current` VALUES (14,'showonline','world','checkbox','Platform','true','ShowOnlineTitle','ShowOnlineComment',NULL,'ShowOnlineWorld'),
		(15,'showonline','users','checkbox','Platform','true','ShowOnlineTitle','ShowOnlineComment',NULL,'ShowOnlineUsers'),
		(16,'showonline','course','checkbox','Platform','true','ShowOnlineTitle','ShowOnlineComment',NULL,'ShowOnlineCourse'),
		(23,'profile','language','checkbox','User','true','ProfileChangesTitle','ProfileChangesComment',NULL,'Language'),
		(27,'registration','language','checkbox','User','true','RegistrationRequiredFormsTitle','RegistrationRequiredFormsComment',NULL,'Language'),	
		(61,'show_navigation_menu',NULL,'radio','Course','true','ShowNavigationMenuTitle','ShowNavigationMenuComment',NULL,NULL),
		(62,'show_icons_in_navigation_menu',NULL,'radio','course','false','ShowIconsInNavigationsMenuTitle','ShowIconsInNavigationsMenuComment',NULL,NULL);");

	mysql_query("INSERT INTO `settings_options` VALUES 
		(62,'show_navigation_menu','true','Yes'),
		(63,'show_navigation_menu','false','No'),
		(64,'show_icons_in_navigation_menu','true','Yes'),
		(65,'show_icons_in_navigation_menu','false','No');");
}

/**
* Creates tables role, basic_right, location,
* user_role and role_right_location.
* Fills tables role, basic_right, location,
* and role_right_location with data.
*/
function add_roles_rights_tables_v17()
{
	mysql_query("DROP TABLE IF EXISTS `role`;");
	mysql_query("CREATE TABLE `role` (
					`id` mediumint(8) unsigned NOT NULL auto_increment,
					`name` varchar(250) default '',
					`type` varchar(40) default 'global',
					`user_id` int(10) unsigned NOT NULL default '0',
					`description` text,
					PRIMARY KEY  (`id`)
					);");

	mysql_query("DROP TABLE IF EXISTS `basic_right`;");
	mysql_query("CREATE TABLE `basic_right` (
					`id` mediumint(8) unsigned NOT NULL auto_increment,
					`name` varchar(250) default '',
					`description` text,
					PRIMARY KEY  (`id`)
					);");

	mysql_query("DROP TABLE IF EXISTS `location`;");
	mysql_query("CREATE TABLE `location` (
					`id` mediumint(8) unsigned NOT NULL auto_increment,
					`location` varchar(250) NOT NULL default '',
					PRIMARY KEY  (`id`)
					);");

	mysql_query("DROP TABLE IF EXISTS `user_role`;");
	mysql_query("CREATE TABLE `user_role` (
					`user_id` int(10) unsigned NOT NULL default '0',
					`role_id` mediumint(8) unsigned NOT NULL default '0',
					`location_id` mediumint(8) unsigned NOT NULL default '0',
					PRIMARY KEY  (`user_id`,`role_id`,`location_id`)
					);");

	mysql_query("DROP TABLE IF EXISTS `role_right_location`;");
	mysql_query("CREATE TABLE `role_right_location` (
					`role_id` mediumint(8) unsigned NOT NULL default '0',
					`right_id` mediumint(8) unsigned NOT NULL default '0',
					`location_id` mediumint(8) unsigned NOT NULL default '0',
					`value` enum('0','1') NOT NULL default '0',
					PRIMARY KEY  (`role_id`,`right_id`,`location_id`)
					);");

	mysql_query("LOCK TABLES `role` WRITE;");
	mysql_query("INSERT INTO `role` VALUES 
		(1,'AnonymousVisitorRole','global',1,'AnonymousVisitorRoleDescription'),
		(2,'StudentRole','global',1,'StudentRoleDescription'),
		(3,'TeacherRole','global',1,'TeacherRoleDescription'),
		(4,'PlatformAdminRole','global',1,'PlatformAdminRoleDescription'),
		(5,'AnonymousGuestCourseMemberRole','local',1,'AnonymousGuestCourseMemberRoleDescription'),
		(6,'RegisteredGuestCourseMemberRole','local',1,'RegisteredGuestCourseMemberRoleDescription'),
		(7,'NormalCourseMemberRole','local',1,'NormalCourseMemberRoleDescription'),
		(8,'TeachingAssistantRole','local',1,'TeachingAssistantRoleDescription'),
		(9,'CourseAdminRole','local',1,'CourseAdminRoleDescription');");
	mysql_query("UNLOCK TABLES;");

	mysql_query("LOCK TABLES `basic_right` WRITE;");
	mysql_query("INSERT INTO `basic_right` VALUES (1,'ViewRight','ViewRightDescription'),(2,'EditRight','EditRightDescription'),(3,'AddRight','AddRightDescription'),(4,'DeleteRight','DeleteRightDescription');");
	mysql_query("UNLOCK TABLES;");

	mysql_query("LOCK TABLES `location` WRITE;");
	mysql_query("INSERT INTO `location` VALUES 
		(1,'platform|courses|course,DEFAULT'),
		(2,'platform|courses|course,DEFAULT|tool,announcement'),
		(3,'platform|courses|course,DEFAULT|tool,backup'),
		(4,'platform|courses|course,DEFAULT|tool,bb_forum'),
		(5,'platform|courses|course,DEFAULT|tool,bb_post'),
		(6,'platform|courses|course,DEFAULT|tool,bb_thread'),
		(7,'platform|courses|course,DEFAULT|tool,calendar_event'),
		(8,'platform|courses|course,DEFAULT|tool,chat'),
		(9,'platform|courses|course,DEFAULT|tool,conference'),
		(10,'platform|courses|course,DEFAULT|tool,copy_course_content'),
		(11,'platform|courses|course,DEFAULT|tool,course_description'),
		(13,'platform|courses|course,DEFAULT|tool,course_rights'),
		(14,'platform|courses|course,DEFAULT|tool,course_setting'),
		(15,'platform|courses|course,DEFAULT|tool,document'),
		(16,'platform|courses|course,DEFAULT|tool,dropbox'),
		(17,'platform|courses|course,DEFAULT|tool,group'),
		(18,'platform|courses|course,DEFAULT|tool,homepage_link'),
		(19,'platform|courses|course,DEFAULT|tool,learnpath'),
		(20,'platform|courses|course,DEFAULT|tool,link'),
		(21,'platform|courses|course,DEFAULT|tool,quiz'),
		(22,'platform|courses|course,DEFAULT|tool,recycle_course'),
		(23,'platform|courses|course,DEFAULT|tool,student_publication'),
		(24,'platform|courses|course,DEFAULT|tool,tracking'),
		(25,'platform|courses|course,DEFAULT|tool,user');");
	mysql_query("UNLOCK TABLES;");

	mysql_query("LOCK TABLES `role_right_location` WRITE;");
	mysql_query("INSERT INTO `role_right_location` VALUES 
		(6, 1, 1, '1'),
		(6, 1, 7, '1'),
		(6, 1, 11, '1'),
		(6, 1, 12, '1'),
		(6, 1, 15, '1'),
		(6, 1, 20, '1'),
		(7, 1, 1, '1'),
		(7, 1, 7, '1'),
		(7, 1, 11, '1'),
		(7, 1, 12, '1'),
		(7, 1, 15, '1'),
		(7, 1, 20, '1'),
		(7, 3, 23, '1'),
		(8, 1, 1, '1'),
		(8, 1, 2, '1'),
		(8, 1, 3, '1'),
		(8, 1, 4, '1'),
		(8, 1, 5, '1'),
		(8, 1, 6, '1'),
		(8, 1, 7, '1'),
		(8, 1, 8, '1'),
		(8, 1, 9, '1'),
		(8, 1, 10, '1'),
		(8, 1, 11, '1'),
		(8, 1, 12, '1'),
		(8, 1, 15, '1'),
		(8, 1, 16, '1'),
		(8, 1, 17, '1'),
		(8, 1, 18, '1'),
		(8, 1, 19, '1'),
		(8, 1, 20, '1'),
		(8, 1, 21, '1'),
		(8, 1, 23, '1'),
		(8, 1, 24, '1'),
		(8, 1, 25, '1'),
		(8, 2, 4, '1'),
		(8, 2, 5, '1'),
		(8, 2, 6, '1'),
		(8, 2, 17, '1'),
		(8, 2, 23, '1'),
		(8, 3, 4, '1'),
		(8, 3, 5, '1'),
		(8, 3, 6, '1'),
		(8, 3, 17, '1'),
		(8, 3, 18, '1'),
		(8, 3, 20, '1'),
		(8, 3, 21, '1'),
		(8, 3, 23, '1'),
		(8, 2, 2, '1'),
		(8, 3, 2, '1'),
		(8, 2, 7, '1'),
		(8, 3, 7, '1'),
		(8, 2, 8, '1'),
		(8, 3, 8, '1'),
		(8, 2, 9, '1'),
		(8, 3, 9, '1'),
		(8, 2, 10, '1'),
		(8, 3, 10, '1'),
		(8, 2, 11, '1'),
		(8, 3, 11, '1'),
		(8, 2, 15, '1'),
		(8, 3, 15, '1'),
		(8, 2, 16, '1'),
		(8, 3, 16, '1'),
		(8, 2, 18, '1'),
		(8, 2, 19, '1'),
		(8, 3, 19, '1'),
		(8, 2, 20, '1'),
		(8, 2, 21, '1'),
		(8, 2, 25, '1'),
		(8, 3, 25, '1'),
		(9, 1, 1, '1'),
		(9, 1, 2, '1'),
		(9, 1, 3, '1'),
		(9, 1, 4, '1'),
		(9, 1, 5, '1'),
		(9, 1, 6, '1'),
		(9, 1, 7, '1'),
		(9, 1, 8, '1'),
		(9, 1, 9, '1'),
		(9, 1, 10, '1'),
		(9, 1, 11, '1'),
		(9, 1, 12, '1'),
		(9, 1, 13, '1'),
		(9, 1, 14, '1'),
		(9, 1, 15, '1'),
		(9, 1, 16, '1'),
		(9, 1, 17, '1'),
		(9, 1, 18, '1'),
		(9, 1, 19, '1'),
		(9, 1, 20, '1'),
		(9, 1, 21, '1'),
		(9, 1, 22, '1'),
		(9, 1, 23, '1'),
		(9, 1, 24, '1'),
		(9, 1, 25, '1'),
		(9, 2, 1, '1'),
		(9, 2, 2, '1'),
		(9, 2, 3, '1'),
		(9, 2, 4, '1'),
		(9, 2, 5, '1'),
		(9, 2, 6, '1'),
		(9, 2, 7, '1'),
		(9, 2, 8, '1'),
		(9, 2, 9, '1'),
		(9, 2, 10, '1'),
		(9, 2, 11, '1'),
		(9, 2, 12, '1'),
		(9, 2, 13, '1'),
		(9, 2, 14, '1'),
		(9, 2, 15, '1'),
		(9, 2, 16, '1'),
		(9, 2, 17, '1'),
		(9, 2, 18, '1'),
		(9, 2, 19, '1'),
		(9, 2, 20, '1'),
		(9, 2, 21, '1'),
		(9, 2, 22, '1'),
		(9, 2, 23, '1'),
		(9, 2, 24, '1'),
		(9, 2, 25, '1'),
		(9, 3, 1, '1'),
		(9, 3, 2, '1'),
		(9, 3, 3, '1'),
		(9, 3, 4, '1'),
		(9, 3, 5, '1'),
		(9, 3, 6, '1'),
		(9, 3, 7, '1'),
		(9, 3, 8, '1'),
		(9, 3, 9, '1'),
		(9, 3, 10, '1'),
		(9, 3, 11, '1'),
		(9, 3, 12, '1'),
		(9, 3, 13, '1'),
		(9, 3, 14, '1'),
		(9, 3, 15, '1'),
		(9, 3, 16, '1'),
		(9, 3, 17, '1'),
		(9, 3, 18, '1'),
		(9, 3, 19, '1'),
		(9, 3, 20, '1'),
		(9, 3, 21, '1'),
		(9, 3, 22, '1'),
		(9, 3, 23, '1'),
		(9, 3, 24, '1'),
		(9, 3, 25, '1'),
		(9, 4, 1, '1'),
		(9, 4, 2, '1'),
		(9, 4, 3, '1'),
		(9, 4, 4, '1'),
		(9, 4, 5, '1'),
		(9, 4, 6, '1'),
		(9, 4, 7, '1'),
		(9, 4, 8, '1'),
		(9, 4, 9, '1'),
		(9, 4, 10, '1'),
		(9, 4, 11, '1'),
		(9, 4, 12, '1'),
		(9, 4, 13, '1'),
		(9, 4, 14, '1'),
		(9, 4, 15, '1'),
		(9, 4, 16, '1'),
		(9, 4, 17, '1'),
		(9, 4, 18, '1'),
		(9, 4, 19, '1'),
		(9, 4, 20, '1'),
		(9, 4, 21, '1'),
		(9, 4, 22, '1'),
		(9, 4, 23, '1'),
		(9, 4, 24, '1'),
		(9, 4, 25, '1');");
	mysql_query("UNLOCK TABLES;");
}

function apply_roles_rights_defaults_to_existing_courses_v17()
{
	//select all courses, including virtual ones
	$sql_result = mysql_query("SELECT * FROM `course`");
	while ($course_result = mysql_fetch_array($sql_result))
	{
		$course_code = $course_result['code'];
		add_course_role_right_location_values($course_code);
	}
}

function add_roles_rights_overview_tool_to_existing_courses_v17()
{
	global $dbGlu,$courseTablePrefix,$singleDbForm;
	//select all courses, including virtual ones
	$sql_result = mysql_query("SELECT * FROM `course`");
	while ($course_result = mysql_fetch_array($sql_result))
	{
		$course_database = $course_result['db_name'];
		if($singleDbForm)
		{
			$tool_table = "`".$courseTablePrefix.$course_database.$dbGlu."tool`";
		}
		else
		{
			$tool_table = "`".$course_database.$dbGlu."tool`";
		}
		$sql = "INSERT INTO $tool_table VALUES ('','".TOOL_COURSE_RIGHTS_OVERVIEW."','course_info/course_rights.php','reference.gif','0','1','','NO','_self')";
		mysql_query($sql);
	}
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
*
* WARNING - DUPLICATION - this function is 99% the same as the function
* found in the add_course.lib.inc.php.
*/
function add_course_role_right_location_values($course_code)
{
	global $dbGlu;
	$location_table = "`location`";
	$relation_table = "`role_right_location`";
	$tool_table = "`".$course_database.$dbGlu."tool`";

	//get default course locations, store into $default_location_list array
	$course_location = "|course,DEFAULT";
	$get_default_location_sql = "SELECT * FROM $location_table WHERE ( location LIKE '%$course_location|%' ) OR ( location LIKE '%$course_location' )";
	$sql_result = api_sql_query($get_default_location_sql);
	while ($result = mysql_fetch_array($sql_result, MYSQL_ASSOC))
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
		mysql_query($add_new_location_sql);
		$new_location_id = mysql_insert_id();

		//find all relations in the table that contained the default location id
		$find_relations_query = "SELECT * FROM $relation_table WHERE location_id='".$default_location['id']."'";
		$sql_result = mysql_query($find_relations_query);
		while ($relation_result = mysql_fetch_array($sql_result, MYSQL_ASSOC))
		{
			//insert new relation with same role id, same right id, new location id, and same value
			$add_new_relation_sql = "INSERT INTO ".$relation_table." VALUES ('".$relation_result['role_id']."', '".$relation_result['right_id']."', '$new_location_id', '".$relation_result['value']."')";
			mysql_query($add_new_relation_sql);
		}
	}
}

/**
* Changes the value for a certain role, certain right in a certain location to
* a new value.
*
* @param $role_id, the id of the role
* @param $right_id, the id of the right
* @param $location, the location
* @param boolean $new_value, the new value to be stored
*
* WARNING - DUPLICATION - this function is 99% the same as the function
* found in the role_right.lib.php.
*/
function set_role_right_value($role_id, $right_id, $location_id, $new_value)
{
	$relation_table = "`role_right_location`";
	$sql_query = "SELECT * FROM $relation_table WHERE right_id='$right_id' AND  role_id='$role_id' AND location_id='$location_id'";
	//echo $sql_query."<br/>";
	$sql_result = mysql_query($sql_query);
	if (mysql_num_rows($sql_result) > 0)
	{
		//an entry already exists, modify it
		$sql_query = "UPDATE $relation_table SET value='$new_value' WHERE right_id='$right_id' AND  role_id='$role_id' AND location_id='$location_id' ";
		//echo $sql_query."<br/>";
		$sql_result = mysql_query($sql_query);
	}
	else
	{
		//no entry exists yet, add one
		$sql_query = "INSERT INTO $relation_table (`role_id`, `right_id`, `location_id`, `value`) VALUES ('$role_id', '$right_id', '$location_id', '$new_value' )";
		//echo $sql_query."<br/>";
		$sql_result = mysql_query($sql_query);
	}
}

/**
* The function apply_roles_rights_defaults_to_existing_courses_v17() should be executed
* before this one to give a complete list of course and course tool default roles-rights
* settings.
*
* This function tries to set the visibility of previously existing tools to
* the same visibility of the older installation, converting older visibility systems
* to the new roles-rights system.
*/
function update_existing_visibility_settings_to_roles_rights_v17()
{
	global $dbGlu;
	$course_table = "`course`";
	$location_table = "`location`";
	$relation_table = "`role_right_location`";

	//select all courses, including virtual ones
	$sql_result = mysql_query("SELECT * FROM $course_table");
	while ($course_result = mysql_fetch_array($sql_result))
	{
		$course_code = $course_result['code'];
		$course_database = $course_result['db_name'];
		$course_visibility = $course_result['visibility'];
		$tool_table = "`".$course_database.$dbGlu."tool`";

		//------ adjust course visibility ------

		$course_location_sql_result = mysql_query("SELECT id FROM $location_table WHERE location LIKE '%|course,$course_code'");
		$course_location_result = mysql_fetch_array($course_location_sql_result);
		$course_location_id = $course_location_result['id'];

		if ($course_visibility == COURSE_VISIBILITY_OPEN_WORLD)
		{
			//echo "$course_code - visib open world, set view right for all roles <br/>";
			set_role_right_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, 1);
			set_role_right_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, 1);
			set_role_right_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $course_location_id, 1);
		}
		else
			if ($course_visibility == COURSE_VISIBILITY_OPEN_PLATFORM)
			{
				//echo "$course_code - visib open platform <br/>";
				set_role_right_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, 0);
				set_role_right_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, 1);
				set_role_right_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $course_location_id, 1);
			}
			else
				if ($course_visibility == COURSE_VISIBILITY_REGISTERED)
				{
					//echo "$course_code - course members only <br/>";
					set_role_right_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, 0);
					set_role_right_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, 0);
					set_role_right_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $course_location_id, 1);
				}
				else
					if ($course_visibility == COURSE_VISIBILITY_CLOSED)
					{
						set_role_right_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, 0);
						set_role_right_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, 0);
						set_role_right_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $course_location_id, 0);
					}
		set_role_right_value(TEACHING_ASSISTANT, VIEW_RIGHT, $course_location_id, 1);
		set_role_right_value(COURSE_ADMIN, VIEW_RIGHT, $course_location_id, 1);

		//------ adjust course tools visibility ------

		//for all course tools in the course tool table,
		//look up their location id in the location table
		//and set their visibility according to the visibility of the tool table
		$select_course_tools_sql = "SELECT * FROM $tool_table";
		$tool_sql_result = mysql_query($select_course_tools_sql);
		while ($current_tool_result = mysql_fetch_array($tool_sql_result))
		{
			$tool_name = $current_tool_result['name'];
			$tool_visibility = $current_tool_result['visibility'];

			$tool_location_sql_result = mysql_query("SELECT id FROM $location_table WHERE location LIKE '%|course,$course_code|tool,$tool_name'");
			$tool_location_result = mysql_fetch_array($tool_location_sql_result);
			$tool_location_id = $tool_location_result['id'];

			//echo "Converting course $course_code (visib $course_visibility), tool $tool_name (visib $tool_visibility, loc id $tool_location_id) to roles-rights system...<br/>";

			$tool_visibility = ($tool_visibility == 1) ? 1 : 0;
			if ($course_visibility == COURSE_VISIBILITY_OPEN_WORLD)
			{
				set_role_right_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $tool_location_id, $tool_visibility);
				set_role_right_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $tool_location_id, $tool_visibility);
				set_role_right_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $tool_location_id, $tool_visibility);
			}
			else
				if ($course_visibility == COURSE_VISIBILITY_OPEN_PLATFORM)
				{
					set_role_right_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $tool_location_id, $tool_visibility);
					set_role_right_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $tool_location_id, $tool_visibility);
					set_role_right_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $tool_location_id, 0);
				}
				else
					if ($course_visibility == COURSE_VISIBILITY_REGISTERED)
					{
						set_role_right_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $tool_location_id, $tool_visibility);
						set_role_right_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $tool_location_id, 0);
						set_role_right_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $tool_location_id, 0);
					}
		}
	}
}

/**
* Users enrolled in a course created in Dokeos 1.6.x don't have a local role yet.
* This function used in the upgrade process from 1.6 to 1.7 assigns a local role
* for all users enrolled in a course.
*/
function assign_local_roles_for_enrolled_users_v17()
{
	$course_user_table = "`course_rel_user`";
	$location_table = "`location`";
	$user_role_table = "`user_role`";

	//get course-user list
	$sql_query = "SELECT * FROM $course_user_table";
	$sql_result = mysql_query($sql_query);
	while ($result = mysql_fetch_array($sql_result))
	{
		$course_user_list[] = $result;
	}

	//assign a local role
	//for every user-course combination
	foreach ($course_user_list as $this_course_user)
	{
		$user_id = $this_course_user['user_id'];
		$status = $this_course_user['status'];
		$role_id = ($status == COURSEMANAGER ? COURSE_ADMIN : NORMAL_COURSE_MEMBER);
		$course_code = $this_course_user['course_code'];
		$short_location_path = "|course,$course_code";
		$find_location_id_sql = "SELECT id from $location_table WHERE location LIKE '%$short_location_path'";
		$sql_result = mysql_query($find_location_id_sql);
		$result = mysql_fetch_array($sql_result);
		$location_id = $result['id'];

		$set_role_sql = "INSERT INTO $user_role_table SET user_id='$user_id', role_id='$role_id', location_id='$location_id'";
		$role_result = mysql_query($set_role_sql);
	}
}

/**
 * Replace the old image names in the database to the new english filenames
 */
function update_image_names_v17()
{
	global $dbGlu,$courseTablePrefix,$singleDbForm;
	// Define the new image names
	$new_image_names['liens.gif'] = 'links.gif';
	$new_image_names['statistiques.gif'] = 'statistics.gif';
	$new_image_names['referencement.gif'] = 'reference.gif';
	$new_image_names['membres.gif'] = 'members.gif';
	//Change the images in the tool table in the main database
	foreach ($new_image_names as $old_image_name => $new_image_name)
	{
		$sql = "UPDATE `course_module` SET image = '".mysql_real_escape_string($new_image_name)."' WHERE image = '".mysql_real_escape_string($old_image_name)."'";
		mysql_query($sql);
	}
	//Change the images in the tool table of every course
	$sql_result = mysql_query("SELECT db_name FROM `course` WHERE target_course_code IS NULL");
	while ($course_result = mysql_fetch_array($sql_result))
	{
		$course_database = $course_result['db_name'];
		if($singleDbForm)
		{
			$tool_table = "`".$courseTablePrefix.$course_database.$dbGlu."tool`";
		}
		else
		{
			$tool_table = "`".$course_database.$dbGlu."tool`";
		}
		foreach ($new_image_names as $old_image_name => $new_image_name)
		{
			$sql = "UPDATE $tool_table SET image = '".mysql_real_escape_string($new_image_name)."' WHERE image = '".mysql_real_escape_string($old_image_name)."'";
			mysql_query($sql);
		}
	}
}

/**
 * Make sure secret_directory field in each group_info table has the same size
 * as the path field in the document table
 */
function update_secret_directory_field_v17()
{
	global $dbGlu,$courseTablePrefix,$singleDbForm;
	//Change the images in the tool table of every course
	$sql_result = mysql_query("SELECT db_name FROM `course` WHERE target_course_code IS NULL");
	while ($course_result = mysql_fetch_array($sql_result))
	{
		$course_database = $course_result['db_name'];
		if($singleDbForm)
		{
			$group_table = "`".$courseTablePrefix.$course_database.$dbGlu."group_info`";
		}
		else
		{
			$group_table = "`".$course_database.$dbGlu."group_info`";
		}
		mysql_query("ALTER TABLE $group_table CHANGE `secret_directory` `secret_directory` VARCHAR(255) DEFAULT NULL");
	}
}
/**
* Function used to upgrade from 1.5.x versions (1.5, 1.5.4, 1.5.5) to 1.6.
* Untested, simply moved code that was outside function to here.
* Guaranteed not to work - needs more input.
*/
function upgrade_v15_to_v16($dbNameForm)
{
	/*
	-----------------------------------------------------------
		Update the main Dokeos database
	-----------------------------------------------------------
	*/
	include ('../lang/english/create_course.inc.php');
	if ($languageForm != 'english')
	{
		include ("../lang/$languageForm/create_course.inc.php");
	}

	mysql_query("CREATE TABLE `$dbNameForm`.`language` (
									 `id` tinyint(3) unsigned NOT NULL auto_increment,
									 `original_name` varchar(255) default NULL,
									 `english_name` varchar(255) default NULL,
									 `isocode` varchar(10) default NULL,
									 `dokeos_folder` varchar(250) default NULL,
									 `available` tinyint(4) NOT NULL default '1',
									 PRIMARY KEY (`id`)
									) TYPE=MyISAM");

	mysql_query("CREATE TABLE `$dbNameForm`.`session` (
									 `sess_id` varchar(32) NOT NULL default '',
									 `sess_name` varchar(10) NOT NULL default '',
									 `sess_time` int(11) NOT NULL default '0',
									 `sess_start` int(11) NOT NULL default '0',
									 `sess_value` text NOT NULL,
									 PRIMARY KEY (`sess_id`)
									) TYPE=MyISAM");

	mysql_query("CREATE TABLE `$dbNameForm`.`settings_current` (
									 `id` int(10) unsigned NOT NULL auto_increment,
									 `variable` varchar(255) default NULL,
									 `subkey` varchar(255) default NULL,
									 `type` varchar(255) default NULL,
									 `category` varchar(255) default NULL,
									 `selected_value` varchar(255) default NULL,
									 `title` varchar(255) NOT NULL default '',
									 `comment` varchar(255) default NULL,
									 `scope` varchar(50) default NULL,
									 `subkeytext` varchar(255) default NULL,
									 UNIQUE KEY `id` (`id`)
									) TYPE=MyISAM");

	mysql_query("CREATE TABLE `$dbNameForm`.`settings_options` (
									 `id` int(10) unsigned NOT NULL auto_increment,
									 `variable` varchar(255) default NULL,
									 `value` varchar(255) default NULL,
									 `display_text` varchar(255) NOT NULL default '',
									 PRIMARY KEY (`id`),
									 UNIQUE KEY `id` (`id`)
									) TYPE=MyISAM");

	mysql_query("CREATE TABLE `$dbNameForm`.`sys_announcement` (
									 `id` int(10) unsigned NOT NULL auto_increment,
									 `date_start` datetime NOT NULL default '0000-00-00 00:00:00',
									 `date_end` datetime NOT NULL default '0000-00-00 00:00:00',
									 `visible_teacher` enum('true','false') NOT NULL default 'false',
									 `visible_student` enum('true','false') NOT NULL default 'false',
									 `visible_guest` enum('true','false') NOT NULL default 'false',
									 `title` varchar(250) NOT NULL default '',
									 `content` text NOT NULL,
									 PRIMARY KEY (`id`)
									) TYPE=MyISAM");

	mysql_query("DROP TABLE `$dbNameForm`.`todo`");
	mysql_query("DROP TABLE `$dbNameForm`.`pma_bookmark`");
	mysql_query("DROP TABLE `$dbNameForm`.`pma_column_comments`");
	mysql_query("DROP TABLE `$dbNameForm`.`pma_pdf_pages`");
	mysql_query("DROP TABLE `$dbNameForm`.`pma_relation`");
	mysql_query("DROP TABLE `$dbNameForm`.`pma_table_coords`");
	mysql_query("DROP TABLE `$dbNameForm`.`pma_table_info`");

	mysql_query("ALTER TABLE `$dbNameForm`.`admin` CHANGE `idUser` `user_id` INT UNSIGNED DEFAULT '0' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`admin` DROP INDEX `idUser`");
	mysql_query("ALTER TABLE `$dbNameForm`.`admin` ADD UNIQUE (`user_id`)");

	mysql_query("ALTER TABLE `$dbNameForm`.`class` ADD `code` VARCHAR(40) DEFAULT '' AFTER `id`");
	mysql_query("ALTER TABLE `$dbNameForm`.`class` CHANGE `name` `name` TEXT NOT NULL");

	mysql_query("ALTER TABLE `$dbNameForm`.`class_user` CHANGE `id_class` `class_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`class_user` CHANGE `id_user` `user_id` INT UNSIGNED DEFAULT '0' NOT NULL");

	mysql_query("ALTER TABLE `$dbNameForm`.`cours` RENAME `$dbNameForm`.`course`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` DROP `cours_id`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `code` `code` VARCHAR(40) NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `directory` `directory` VARCHAR(40) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `dbName` `db_name` VARCHAR(40) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `languageCourse` `course_language` VARCHAR(20) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `intitule` `title` VARCHAR(250) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `faculte` `category_code` VARCHAR(40) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `visible` `visibility` TINYINT(4) DEFAULT '0'");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` DROP `cahier_charges`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `scoreShow` `show_score` INT(11) DEFAULT '1' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `titulaires` `tutor_name` VARCHAR(200) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `fake_code` `visual_code` VARCHAR(40) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `departmentUrlName` `department_name` VARCHAR(30) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `departmentUrl` `department_url` VARCHAR(180) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `diskQuota` `disk_quota` INT(10) UNSIGNED DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` DROP `versionDb`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` DROP `versionClaro`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `lastVisit` `last_visit` DATETIME DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `lastEdit` `last_edit` DATETIME DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `creationDate` `creation_date` DATETIME DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` CHANGE `expirationDate` `expiration_date` DATETIME DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` ADD `target_course_code` VARCHAR(40)");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` ADD `subscribe` TINYINT(4) DEFAULT '1' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` ADD `unsubscribe` TINYINT(4) DEFAULT '1' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` DROP PRIMARY KEY");
	mysql_query("ALTER TABLE `$dbNameForm`.`course` ADD PRIMARY KEY (`code`)");

	mysql_query("UPDATE `$dbNameForm`.`course` SET visibility='1' WHERE visibility='0'");
	mysql_query("UPDATE `$dbNameForm`.`course` SET visibility='3' WHERE visibility='2'");

	mysql_query("ALTER TABLE `$dbNameForm`.`faculte` RENAME `$dbNameForm`.`course_category`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` CHANGE `code_P` `parent_id` VARCHAR(40) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` DROP `bc`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` CHANGE `treePos` `tree_pos` INT(10) UNSIGNED DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` CHANGE `nb_childs` `children_count` SMALLINT(6) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` CHANGE `canHaveCoursesChild` `auth_course_child` ENUM('TRUE', 'FALSE') DEFAULT 'TRUE'");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` CHANGE `canHaveCatChild` `auth_cat_child` ENUM('TRUE', 'FALSE') DEFAULT 'TRUE'");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` DROP INDEX `code_P`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` DROP INDEX `treePos`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` ADD UNIQUE (`code`)");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` ADD INDEX (`parent_id`)");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_category` ADD INDEX (`tree_pos`)");

	mysql_query("ALTER TABLE `$dbNameForm`.`tools_basic` RENAME `$dbNameForm`.`course_module`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_module` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_module` CHANGE `rubrique` `name` VARCHAR(100) NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_module` CHANGE `lien` `link` VARCHAR(255) NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_module` CHANGE `row` `row` INT(10) UNSIGNED DEFAULT '0' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_module` CHANGE `column` `column` INT(10) UNSIGNED DEFAULT '0' NOT NULL");

	mysql_query("ALTER TABLE `$dbNameForm`.`cours_class` RENAME `$dbNameForm`.`course_rel_class`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_rel_class` CHANGE `code_cours` `course_code` CHAR(40) NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_rel_class` CHANGE `id_class` `class_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL");

	mysql_query("ALTER TABLE `$dbNameForm`.`cours_user` RENAME `$dbNameForm`.`course_rel_user`");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_rel_user` CHANGE `code_cours` `course_code` VARCHAR(40) NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_rel_user` CHANGE `statut` `status` TINYINT(4) DEFAULT '5' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_rel_user` CHANGE `user_id` `user_id` INT UNSIGNED DEFAULT '0' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_rel_user` CHANGE `team` `group_id` INT(11) DEFAULT '0' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_rel_user` CHANGE `tutor` `tutor_id` INT UNSIGNED DEFAULT '0' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_rel_user` ADD `sort` INT");
	mysql_query("ALTER TABLE `$dbNameForm`.`course_rel_user` ADD `user_course_cat` INT DEFAULT '0'");

	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `nom` `lastname` VARCHAR(60) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `prenom` `firstname` VARCHAR(60) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `username` `username` VARCHAR(20) NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `password` `password` VARCHAR(50) NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `authSource` `auth_source` VARCHAR(50) DEFAULT '".PLATFORM_AUTH_SOURCE."'");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `statut` `status` TINYINT(4) DEFAULT '5' NOT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `officialCode` `official_code` VARCHAR(40) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `phoneNumber` `phone` VARCHAR(30) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `pictureUri` `picture_uri` VARCHAR(250) DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` CHANGE `creatorId` `creator_id` INT UNSIGNED DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` ADD `competences` TEXT AFTER `creator_id`");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` ADD `diplomas` TEXT AFTER `competences`");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` ADD `openarea` TEXT AFTER `diplomas`");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` ADD `teach` TEXT AFTER `openarea`");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` ADD `productions` VARCHAR(250) AFTER `teach`");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` ADD `chatcall_user_id` INT UNSIGNED NOT NULL AFTER `productions`");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` ADD `chatcall_date` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER `chatcall_user_id`");
	mysql_query("ALTER TABLE `$dbNameForm`.`user` ADD `chatcall_text` VARCHAR(50) NOT NULL AFTER `chatcall_date`");

	$language_table = "`$dbNameForm`.`language`";
	fill_language_table($language_table);

	$installation_settings['institution_form'] = $institutionForm;
	$installation_settings['institution_url_form'] = $institutionUrlForm;
	$installation_settings['campus_form'] = $campusForm;
	$installation_settings['email_form'] = $emailForm;
	$installation_settings['admin_last_name'] = $adminLastName;
	$installation_settings['admin_first_name'] = $adminFirstName;
	$installation_settings['language_form'] = $languageForm;
	$installation_settings['allow_self_registration'] = $allowSelfReg;
	$installation_settings['allow_teacher_self_registration'] = $allowSelfRegProf;
	$installation_settings['admin_phone_form'] = $adminPhoneForm;

	$current_settings_table = "`$dbNameForm`.`settings_current`";
	fill_current_settings_table($current_settings_table, $installation_settings);

	$settings_options_table = "`$dbNameForm`.`settings_options`";
	fill_settings_options_table($settings_options_table);

	mysql_query("INSERT INTO `$dbNameForm`.`course_module` (`name`,`link`,`image`,`row`,`column`,`position`) VALUES
										('AddedLearnpath', NULL, 'scormbuilder.gif', 0, 0, 'external'),
										('".TOOL_BACKUP."', 'coursecopy/backup.php' , 'backup.gif', 2, 1, 'courseadmin'),
										('".TOOL_COPY_COURSE_CONTENT."', 'coursecopy/copy_course.php' , 'copy.gif', 2, 2, 'courseadmin'),
										('".TOOL_RECYCLE_COURSE."', 'coursecopy/recycle_course.php' , 'recycle.gif', 2, 3, 'courseadmin')");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_COURSE_DESCRIPTION."' WHERE link LIKE 'course_description/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_CALENDAR_EVENT."' WHERE link LIKE 'calendar/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_DOCUMENT."' WHERE link LIKE 'document/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_ANNOUNCEMENT."' WHERE link LIKE 'announcements/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_BB_FORUM."' WHERE link LIKE 'phpbb/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_LINK."' WHERE link = 'link/link.php'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_DROPBOX."' WHERE link LIKE 'dropbox/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_QUIZ."' WHERE link LIKE 'exercice/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_USER."' WHERE link LIKE 'user/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_GROUP."' WHERE link LIKE 'group/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_CHAT."' WHERE link LIKE 'chat/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_CONFERENCE."' WHERE link LIKE 'online/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_STUDENTPUBLICATION."' WHERE link LIKE 'work/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_TRACKING."' WHERE link LIKE 'tracking/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_HOMEPAGE_LINK."' WHERE link = 'link/link.php?action=addlink'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_COURSE_SETTING."' WHERE link LIKE 'course_info/%'");
	mysql_query("UPDATE `$dbNameForm`.`course_module` SET name='".TOOL_LEARNPATH."' WHERE link LIKE 'scorm/%'");

	// existing courses should have a value entered for sort into the course_rel_user table
	$tbl_user = "`$dbNameForm`.`user`";
	$tbl_course_user = "`$dbNameForm`.`course_rel_user`";

	$sqlusers = "SELECT * FROM $tbl_user";
	$resultusers = api_sql_query($sqlusers);
	while ($row = mysql_fetch_array($resultusers))
	{
		$counter = 1;
		$sql_course_user = "SELECT * FROM $tbl_course_user WHERE user_id='".$row['user_id']."'";
		$result_course_user = api_sql_query($sql_course_user);
		while ($rowcu = mysql_fetch_array($result_course_user))
		{
			$update = "UPDATE $tbl_course_user SET sort='$counter' WHERE user_id='".$row['user_id']."' AND course_code='".$rowcu['course_code']."'";
			$resultupdate = api_sql_query($update);
			$counter ++;
		}
	}

	/*
	-----------------------------------------------------------
		Update the tracking Dokeos database
	-----------------------------------------------------------
	*/
	mysql_query("CREATE TABLE `$dbStatsForm`.`track_e_hotpotatoes` (
								`exe_name` varchar(255) NOT NULL default '',
								`exe_user_id` int unsigned default NULL,
								`exe_date` datetime NOT NULL default '0000-00-00 00:00:00',
								`exe_cours_id` varchar(20) NOT NULL default '',
								`exe_result` tinyint(4) NOT NULL default '0',
								`exe_weighting` tinyint(4) NOT NULL default '0'
							) TYPE=MyISAM");

	mysql_query("CREATE TABLE `$dbStatsForm`.`track_e_online` (
								`login_id` int(11) NOT NULL auto_increment,
								`login_user_id` int unsigned NOT NULL default '0',
								`login_date` datetime NOT NULL default '0000-00-00 00:00:00',
								`login_ip` varchar(39) NOT NULL default '',
								PRIMARY KEY (`login_id`),
								KEY `login_user_id` (`login_user_id`)
							) TYPE=MyISAM");

	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_access` CHANGE `access_user_id` `access_user_id` INT UNSIGNED DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_default` CHANGE `default_user_id` `default_user_id` INT UNSIGNED DEFAULT '0' NOT NULL");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_downloads` CHANGE `down_user_id` `down_user_id` INT UNSIGNED DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_exercices` CHANGE `exe_user_id` `exe_user_id` INT UNSIGNED DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_exercices` CHANGE `exe_cours_id` `exe_cours_id` VARCHAR(40) NOT NULL DEFAULT ''");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_exercices` CHANGE `exe_exo_id` `exe_exo_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_exercices` CHANGE `exe_result` `exe_result` SMALLINT NOT NULL DEFAULT '0'");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_exercices` CHANGE `exe_weighting` `exe_weighting` SMALLINT NOT NULL DEFAULT '0'");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_hotpotatoes` CHANGE `exe_cours_id` `exe_cours_id` VARCHAR(40) NOT NULL DEFAULT ''");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_hotpotatoes` CHANGE `exe_result` `exe_result` SMALLINT NOT NULL DEFAULT '0'");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_hotpotatoes` CHANGE `exe_weighting` `exe_weighting` SMALLINT NOT NULL DEFAULT '0'");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_lastaccess` CHANGE `access_user_id` `access_user_id` INT UNSIGNED DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_links` CHANGE `links_user_id` `links_user_id` INT UNSIGNED DEFAULT NULL");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_login` CHANGE `login_user_id` `login_user_id` INT UNSIGNED DEFAULT '0' NOT NULL");
	mysql_query("ALTER TABLE `$dbStatsForm`.`track_e_uploads` CHANGE `upload_user_id` `upload_user_id` INT UNSIGNED DEFAULT NULL");

	/*
	-----------------------------------------------------------
		Create the User database
	-----------------------------------------------------------
	*/
	$sql = "CREATE DATABASE IF NOT EXISTS `$dbUserForm`";
	mysql_query($sql);

	mysql_query("CREATE TABLE `$dbUserForm`.`personal_agenda` (
							`id` int NOT NULL auto_increment,
							`user` int unsigned,
							`title` text,
							`text` text,
							`date` datetime default NULL,
							`enddate` datetime default NULL,
							`course` varchar(255),
							UNIQUE KEY `id` (`id`))
							TYPE=MyISAM");

	mysql_query("CREATE TABLE `$dbUserForm`.`user_course_category` (
							`id` int unsigned NOT NULL auto_increment,
							`user_id` int unsigned NOT NULL default '0',
							`title` text NOT NULL,
							PRIMARY KEY  (`id`)
							) TYPE=MyISAM");

	/*
	-----------------------------------------------------------
		Update the Dokeos course databases
		this part can be accessed in two ways:
		- from the normal upgrade process
		- from the script update_courses.php,
		which is used to upgrade more than MAX_COURSE_TRANSFER courses
	
		Every time this script is accessed, only
		MAX_COURSE_TRANSFER courses are upgraded.
	-----------------------------------------------------------
	*/
	$newPath = str_replace('\\', '/', realpath('../..')).'/';

	$coursePath = array ();
	$courseDB = array ();
	$nbr_courses = 0;

	if ($result = mysql_query("SELECT code,db_name,directory,course_language FROM `$dbNameForm`.`course` WHERE target_course_code IS NULL"))
	{
		$i = 0;

		$nbr_courses = mysql_num_rows($result);

		while ($i < MAX_COURSE_TRANSFER && (list ($course_code, $mysql_base_course, $directory, $languageCourse) = mysql_fetch_row($result)))
		{
			if (!file_exists($newPath.'courses/'.$directory))
			{
				if ($singleDbForm)
				{
					$prefix = $courseTablePrefix.$mysql_base_course.$dbGlu;

					$mysql_base_course = $dbNameForm.'`.`'.$courseTablePrefix.$mysql_base_course;
				}
				else
				{
					$prefix = '';
				}

				$coursePath[$course_code] = $directory;
				$courseDB[$course_code] = $mysql_base_course;

				include ("../lang/english/create_course.inc.php");

				if ($languageCourse != 'english')
				{
					include ("../lang/$languageCourse/create_course.inc.php");
				}

				mysql_query("CREATE TABLE `$mysql_base_course".$dbGlu."chat_connected` (
																				 `user_id` int unsigned NOT NULL default '0',
																				 `last_connection` datetime NOT NULL default '0000-00-00 00:00:00',
																				 PRIMARY KEY (`user_id`)
																				) TYPE=MyISAM");

				mysql_query("CREATE TABLE `$mysql_base_course".$dbGlu."online_connected` (
																				 `user_id` int unsigned NOT NULL default '0',
																				 `last_connection` datetime NOT NULL default '0000-00-00 00:00:00',
																				 PRIMARY KEY (`user_id`)
																				) TYPE=MyISAM");

				mysql_query("CREATE TABLE `$mysql_base_course".$dbGlu."online_link` (
																				 `id` smallint(5) unsigned NOT NULL auto_increment,
																				 `name` char(50) NOT NULL default '',
																				 `url` char(100) NOT NULL default '',
																				 PRIMARY KEY (`id`)
																				) TYPE=MyISAM");

				mysql_query("DROP TABLE `$mysql_base_course".$dbGlu."online`");

				mysql_query("DROP TABLE `$mysql_base_course".$dbGlu."pages`");

				mysql_query("DROP TABLE `$mysql_base_course".$dbGlu."work_student`");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."last_tooledit` RENAME `".$prefix."item_property`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` CHANGE `last_date` `lastedit_date` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` CHANGE `ref` `ref` INT(10) NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` CHANGE `type` `lastedit_type` VARCHAR(100) NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` CHANGE `user_id` `lastedit_user_id` INT UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` CHANGE `group_id` `to_group_id` INT(10) UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` ADD `to_user_id` INT UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` ADD `visibility` TINYINT(1) DEFAULT '1' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` ADD `start_visible` DATETIME NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` ADD `end_visible` DATETIME NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` ADD `insert_user_id` INT UNSIGNED NOT NULL AFTER `tool`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."item_property` ADD `insert_date` DATETIME NOT NULL AFTER `insert_user_id`");

				/*
				-----------------------------------------------------------
				Update the announcement table
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."announcement` CHANGE `contenu` `content` TEXT DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."announcement` CHANGE `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."announcement` CHANGE `temps` `end_date` DATE DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."announcement` DROP `code_cours`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."announcement` CHANGE `ordre` `display_order` MEDIUMINT(9) DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."announcement` ADD `title` TEXT AFTER `id`");
				// Set item-properties of announcements and generate a title for the announcement
				$sql = "SELECT id,end_date,content FROM `$mysql_base_course".$dbGlu."announcement`";
				$res = mysql_query($sql);
				while ($obj = mysql_fetch_object($res))
				{
					$content_parts = explode('<br>', trim($obj->content));
					$title = strip_tags($content_parts[0]);
					if (strlen(trim($title)) == 0)
					{
						$title = substr(strip_tags($title), 0, 50).'...';
					}
					$sql = "UPDATE `$mysql_base_course".$dbGlu."announcement` SET title = '".mysql_real_escape_string($title)."' WHERE id='".$obj->id."'";
					mysql_query($sql);
					$sql = "INSERT INTO `$mysql_base_course".$dbGlu."item_property` SET ";
					$sql .= " tool = '".TOOL_ANNOUNCEMENT."', ";
					$sql .= " insert_date = '".$obj->end_date." 00:00:00', ";
					$sql .= " lastedit_date = '".$obj->end_date." 00:00:00', ";
					$sql .= " ref = '".$obj->id."', ";
					$sql .= " lastedit_type = 'AnnouncementAdded', ";
					$sql .= " to_group_id = '0' ";
					mysql_query($sql);
				}

				/*
				-----------------------------------------------------------
				Update the bb_whosonline table
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."bb_whosonline` CHANGE `date` `online_date` VARCHAR(255) DEFAULT NULL");

				/*
				-----------------------------------------------------------
				Update the calendar_event table
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."calendar_event` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."calendar_event` CHANGE `titre` `title` VARCHAR(200) NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."calendar_event` CHANGE `contenu` `content` TEXT DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."calendar_event` CHANGE `day` `start_date` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."calendar_event` DROP `hour`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."calendar_event` DROP `lasting`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."calendar_event` ADD `end_date` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
				// Set start-date equal to end date if end date is 0000-00-00 00:00:00
				$sql = "UPDATE `$mysql_base_course".$dbGlu."calendar_event` SET end_date = start_date WHERE end_date = '0000-00-00 00:00:00'";
				mysql_query($sql);
				// Set item-properties of calendar events
				$sql = "SELECT id,start_date FROM `$mysql_base_course".$dbGlu."calendar_event`";
				$res = mysql_query($sql);
				while ($obj = mysql_fetch_object($res))
				{
					$sql = "INSERT INTO `$mysql_base_course".$dbGlu."item_property` SET ";
					$sql .= " tool = '".TOOL_CALENDAR_EVENT."', ";
					$sql .= " insert_date = NOW(), ";
					$sql .= " lastedit_date = NOW(), ";
					$sql .= " ref = '".$obj->id."', ";
					$sql .= " lastedit_type = 'AgendaAdded', ";
					$sql .= " to_group_id = '0' ";
					mysql_query($sql);
				}

				/*
				-----------------------------------------------------------
				Update the course_description table
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."course_description` CHANGE `id` `id` TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."course_description` DROP `upDate`");

				/*
				-----------------------------------------------------------
				Update the document table
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."document` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."document` CHANGE `comment` `comment` TEXT DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."document` ADD `title` VARCHAR(255) AFTER `comment`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."document` ADD `size` INT(16) NOT NULL");
				// @note: Item properties of documents are set in update_files.inc.php

				/*
				-----------------------------------------------------------
				Update the dropbox tables
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_file` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_file` CHANGE `uploaderId` `uploader_id` INT(10) UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_file` CHANGE `filesize` `filesize` INT(10) UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_file` CHANGE `uploadDate` `upload_date` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_file` CHANGE `lastUploadDate` `last_upload_date` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_person` CHANGE `fileId` `file_id` INT(10) UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_person` CHANGE `personId` `user_id` INT UNSIGNED DEFAULT '0' NOT NULL");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_post` CHANGE `fileId` `file_id` INT(10) UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_post` CHANGE `recipientId` `dest_user_id` INT UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_post` ADD `feedback_date` DATETIME NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."dropbox_post` ADD `feedback` TEXT");

				// Set item-properties of dropbox files
				$sql = "SELECT * FROM `$mysql_base_course".$dbGlu."dropbox_file` f, `$mysql_base_course".$dbGlu."dropbox_post` p WHERE f.id = p.file_id";
				$res = mysql_query($sql);
				while ($obj = mysql_fetch_object($res))
				{
					$sql = "INSERT INTO `$mysql_base_course".$dbGlu."item_property` SET ";
					$sql .= " tool = '".TOOL_DROPBOX."', ";
					$sql .= " insert_date = '".$obj->upload_date."', ";
					$sql .= " lastedit_date = '".$obj->last_upload_date."', ";
					$sql .= " ref = '".$obj->id."', ";
					$sql .= " lastedit_type = 'DropboxFileAdded', ";
					$sql .= " to_group_id = '0', ";
					$sql .= " to_user_id = '".$obj->dest_user_id."', ";
					$sql .= " insert_user_id = '".$obj->uploader_id."'";
					mysql_query($sql);
				}

				/*
				-----------------------------------------------------------
				Update the group tables
				-----------------------------------------------------------
				*/
				mysql_query("CREATE TABLE `$mysql_base_course".$dbGlu."group_category` (
																				 `id` int(10) unsigned NOT NULL auto_increment,
																				 `title` varchar(255) NOT NULL default '',
																				 `description` text NOT NULL,
																				 `forum_state` tinyint(3) unsigned NOT NULL default '1',
																				 `doc_state` tinyint(3) unsigned NOT NULL default '1',
																				 `max_student` smallint(5) unsigned NOT NULL default '8',
																				 `self_reg_allowed` enum('0','1') NOT NULL default '0',
																				 `self_unreg_allowed` enum('0','1') NOT NULL default '0',
																				 `groups_per_user` smallint(5) unsigned NOT NULL default '0',
																				 `display_order` smallint(5) unsigned NOT NULL default '0',
																				 PRIMARY KEY (`id`)
																				) TYPE=MyISAM");

				// Get the group-properties from old portal
				$sql = "SELECT * FROM `$mysql_base_course".$dbGlu."group_property`";
				$res = mysql_query($sql);

				$group_properties = mysql_fetch_array($res, MYSQL_ASSOC);

				mysql_query("DROP TABLE `$mysql_base_course".$dbGlu."group_property`");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_team` RENAME `".$prefix."group_info`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_info` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_info` CHANGE `tutor` `tutor_id` MEDIUMINT(8) UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_info` CHANGE `forumId` `forum_id` INT(10) UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_info` CHANGE `maxStudent` `max_student` SMALLINT(5) UNSIGNED DEFAULT '8' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_info` CHANGE `secretDirectory` `secret_directory` VARCHAR(200) DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_info` ADD `self_registration_allowed` ENUM('0', '1') DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_info` ADD `self_unregistration_allowed` ENUM('0', '1') DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_info` ADD `category_id` INT(10) UNSIGNED NOT NULL AFTER `name`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_info` ADD `forum_state` ENUM('0', '1', '2') DEFAULT '0' NOT NULL AFTER `tutor_id`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_info` ADD `doc_state` ENUM('0', '1', '2') DEFAULT '0' NOT NULL AFTER `max_student`");
				// Update group-properties (doc_state = always private, forum_state ~ old group properties, category_id = default category)
				$forum_state = ($group_properties['private']) == '0' ? '1' : '2';
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."group_info` SET category_id='2', doc_state='2', forum_state = '".$forum_state."', secret_directory = CONCAT('/',secret_directory)");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."group_info` SET tutor_id='0' WHERE tutor_id IS NULL");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_rel_team_user` RENAME `".$prefix."group_rel_user`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_rel_user` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_rel_user` CHANGE `user` `user_id` INT UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_rel_user` CHANGE `team` `group_id` INT(10) UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."group_rel_user` CHANGE `role` `role` CHAR(50) NOT NULL");

				mysql_query("INSERT INTO `$mysql_base_course".$dbGlu."group_category` (`id`,`title`,`groups_per_user`) VALUES ('2','".get_lang('DefaultGroupCategory')."','".$group_properties['nbCoursPerUser']."')");

				/*
				-----------------------------------------------------------
				Update the learnpath tables
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_chapters` RENAME `".$prefix."learnpath_chapter`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_chapter` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_chapter` CHANGE `learnpath_id` `learnpath_id` INT(10) UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_chapter` CHANGE `ordre` `display_order` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_chapter` ADD `parent_chapter_id` INT UNSIGNED DEFAULT 0 NOT NULL AFTER `chapter_description`");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_items` RENAME `".$prefix."learnpath_item`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_item` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_item` CHANGE `chapter` `chapter_id` INT(10) UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_item` CHANGE `item_id` `item_id` INT(10) UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_item` CHANGE `ordre` `display_order` SMALLINT(6) DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_item` CHANGE `prereq` `prereq_id` INT(10) UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_item` ADD `prereq_completion_limit` VARCHAR(10) DEFAULT NULL");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_main` CHANGE `learnpath_id` `learnpath_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_users` RENAME `".$prefix."learnpath_user`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_user` CHANGE `user_id` `user_id` INT UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_user` CHANGE `learnpath_id` `learnpath_id` INT(10) UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_user` CHANGE `learnpath_item_id` `learnpath_item_id` INT(10) UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."learnpath_user` CHANGE `score` `score` SMALLINT(6) DEFAULT NULL");

				/*
				-----------------------------------------------------------
				Update the link tables
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."link` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."link` CHANGE `url` `url` TEXT NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."link` CHANGE `titre` `title` VARCHAR(150) DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."link` CHANGE `category` `category_id` SMALLINT(5) UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."link` CHANGE `ordre` `display_order` SMALLINT(5) UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."link` ADD `on_homepage` ENUM('0', '1') DEFAULT '0' NOT NULL");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."link_categories` RENAME `".$prefix."link_category`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."link_category` CHANGE `id` `id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."link_category` CHANGE `categoryname` `category_title` VARCHAR(255) NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."link_category` CHANGE `ordre` `display_order` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL");

				// Set item-properties of links
				$sql = "SELECT id FROM `$mysql_base_course".$dbGlu."link`";
				$res = mysql_query($sql);
				while ($obj = mysql_fetch_object($res))
				{
					$sql = "INSERT INTO `$mysql_base_course".$dbGlu."item_property` SET ";
					$sql .= " tool = '".TOOL_LINK."', ";
					$sql .= " insert_date = NOW(), ";
					$sql .= " lastedit_date = NOW(), ";
					$sql .= " ref = '".$obj->id."', ";
					$sql .= " lastedit_type = 'LinkAdded', ";
					$sql .= " to_group_id = '0' ";
					mysql_query($sql);
				}

				// move all the links on the course homepage to the links tool
				// step 1: count the max display order of the 0 category_id
				$sql = "SELECT * FROM `$mysql_base_course".$dbGlu."link` WHERE category_id='0' ORDER BY display_order DESC";
				$result2 = mysql_query($sql);
				$row = mysql_fetch_array($result2);
				$maxsort = $row['display_order'];

				// step 2: select all the links that were added to the course homepage
				$sql = "SELECT * FROM `$mysql_base_course".$dbGlu."tool` WHERE link LIKE 'http://%'";
				$result2 = mysql_query($sql);
				while ($row = mysql_fetch_array($result2))
				{
					$maxsort ++;
					// step 3: for each link on homepage: add to the links table
					$sqlinsert = "INSERT INTO `$mysql_base_course".$dbGlu."link` (url, title, category_id, display_order, on_homepage) VALUES('".$row['link']."','".$row['name']."','0','".$maxsort."','1')";
					$resultinsert = mysql_query($sqlinsert);
					$insertid = mysql_insert_id();

					// step 4: for each link on homepage: add the link in the item_property table
					$sql_item_property = "INSERT INTO `$mysql_base_course".$dbGlu."item_property` SET ";
					$sql_item_property .= " tool = '".TOOL_LINK."', ";
					$sql_item_property .= " ref = '".$insertid."', ";
					$sql_item_property .= " lastedit_type = 'LinkAdded', ";
					$sql_item_property .= " to_group_id = '0' ";
					api_sql_query($sql_item_property);

					// step 5: for each link on homepage: delete the link in the tool table.				
					$sqldelete = "DELETE FROM `$mysql_base_course".$dbGlu."tool` WHERE id='".$row['id']."'";
					$resultdelete = mysql_query($sqldelete);
				}

				/*
				-----------------------------------------------------------
				Update the quiz tables
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz_rel_test_question` RENAME `".$prefix."quiz_rel_question`");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz_test` RENAME `".$prefix."quiz`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz` CHANGE `titre` `title` VARCHAR(200) NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz` CHANGE `description` `description` TEXT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz` CHANGE `sound` `sound` VARCHAR(50)");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz` CHANGE `type` `type` TINYINT(3) UNSIGNED DEFAULT '1' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz` CHANGE `active` `active` ENUM('0', '1') DEFAULT '0' NOT NULL");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz_answer` CHANGE `reponse` `answer` TEXT NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz_answer` CHANGE `ponderation` `ponderation` SMALLINT(6) DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz_answer` CHANGE `r_position` `position` MEDIUMINT(8) UNSIGNED DEFAULT '1' NOT NULL");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz_question` CHANGE `description` `description` TEXT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz_question` CHANGE `q_position` `position` MEDIUMINT(8) UNSIGNED DEFAULT '1' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."quiz_question` CHANGE `picture` `picture` VARCHAR(50)");

				/*
				-----------------------------------------------------------
				Update the resource linker table
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."added_resources` RENAME `".$prefix."resource`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."resource` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."resource` CHANGE `source_id` `source_id` INT(10) UNSIGNED DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."resource` CHANGE `resource_id` `resource_id` INT(10) UNSIGNED DEFAULT NULL");

				/*
				-----------------------------------------------------------
				Update the scormdocument table
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."scormdocument` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."scormdocument` ADD `name` VARCHAR(100)");

				/*
				-----------------------------------------------------------
				Update the student_publication table
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."assignment_doc` RENAME `".$prefix."student_publication`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."student_publication` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."student_publication` CHANGE `titre` `title` VARCHAR(200) DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."student_publication` CHANGE `auteurs` `author` VARCHAR(200) DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."student_publication` CHANGE `active` `active` TINYINT(4) DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."student_publication` CHANGE `accepted` `accepted` TINYINT(4) DEFAULT '0'");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."student_publication` CHANGE `date` `sent_date` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");

				/*
				-----------------------------------------------------------
				Update the tool introduction table
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."tool_intro` CHANGE `id` `id` VARCHAR(50) NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."tool_intro` CHANGE `texte_intro` `intro_text` TEXT NOT NULL");

				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool_intro` SET id='".TOOL_COURSE_HOMEPAGE."' WHERE id = '1'");

				/*
				-----------------------------------------------------------
				Update the user information tables
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."userinfo_content` CHANGE `user_id` `user_id` INT(10) UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."userinfo_content` CHANGE `def_id` `definition_id` INT(10) UNSIGNED DEFAULT '0' NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."userinfo_content` CHANGE `ed_ip` `editor_ip` VARCHAR(39) DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."userinfo_content` CHANGE `ed_date` `edition_time` DATETIME DEFAULT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."userinfo_content` CHANGE `content` `content` TEXT NOT NULL");

				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."userinfo_def` CHANGE `nbLine` `line_count` TINYINT(3) UNSIGNED DEFAULT '5' NOT NULL");

				/*
				-----------------------------------------------------------
				Update the tool table
				-----------------------------------------------------------
				*/
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."tool_list` RENAME `".$prefix."tool`");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."tool` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."tool` CHANGE `rubrique` `name` VARCHAR(100) NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."tool` CHANGE `lien` `link` VARCHAR(255) NOT NULL");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."tool` CHANGE `visible` `visibility` TINYINT(3) UNSIGNED DEFAULT '0'");
				mysql_query("ALTER TABLE `$mysql_base_course".$dbGlu."tool` CHANGE `addedTool` `added_tool` ENUM('0', '1') DEFAULT '0'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_COURSE_DESCRIPTION."' WHERE link LIKE 'course_description/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_CALENDAR_EVENT."' WHERE link LIKE 'calendar/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_DOCUMENT."' WHERE link LIKE 'document/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_ANNOUNCEMENT."' WHERE link LIKE 'announcements/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_BB_FORUM."' WHERE link LIKE 'phpbb/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_LINK."' WHERE link = 'link/link.php'");
				mysql_query("Update `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_DROPBOX."' WHERE link LIKE 'dropbox/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_QUIZ."' WHERE link LIKE 'exercice/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_USER."' WHERE link LIKE 'user/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_GROUP."' WHERE link LIKE 'group/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_CHAT."' WHERE link LIKE 'chat/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_CONFERENCE."' WHERE link LIKE 'online/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_STUDENTPUBLICATION."' WHERE link LIKE 'work/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_TRACKING."' WHERE link LIKE 'tracking/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_COURSE_SETTING."' WHERE link LIKE 'course_info/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_LEARNPATH."' WHERE link LIKE 'scorm/%'");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET name='".TOOL_HOMEPAGE_LINK."', link='link/link.php?action=addlink' WHERE link LIKE 'external_module/%'");
				//mysql_query("INSERT INTO `$mysql_base_course".$dbGlu."tool` (`id`, `name`, `link`, `image`, `visibility`, `admin`, `address`, `added_tool`, `target`) VALUES ('', '".TOOL_BACKUP."', 'coursecopy/backup.php', 'backup.gif', '0', '1', '', '0', '_self')");
				mysql_query("INSERT INTO `$mysql_base_course".$dbGlu."tool` (`id`, `name`, `link`, `image`, `visibility`, `admin`, `address`, `added_tool`, `target`) VALUES ('', '".TOOL_COPY_COURSE_CONTENT."', 'coursecopy/copy_course.php', 'copy.gif', '0', '1', '', '0', '_self')");
				//mysql_query("INSERT INTO `$mysql_base_course".$dbGlu."tool` (`id`, `name`, `link`, `image`, `visibility`, `admin`, `address`, `added_tool`, `target`) VALUES ('', '".TOOL_RECYCLE_COURSE."', 'coursecopy/recycle_course.php', 'recycle.gif', '0', '1', '', '0', '_self')");
				mysql_query("UPDATE `$mysql_base_course".$dbGlu."tool` SET `added_tool` = '0' WHERE `added_tool` = ''");

				$i ++;
			}
			else
			{
				$nbr_courses --;
			}
		} //end while
	} //end if (mysql query)
} //end function upgrade_v15_to_v16($dbNameForm)

/*
==============================================================================
		MAIN CODE
==============================================================================
*/

if (defined('DOKEOS_INSTALL') || defined('DOKEOS_COURSE_UPDATE'))
{
	if (empty ($updateFromConfigFile) || !file_exists($_POST['updatePath'].$updateFromConfigFile) || !in_array(get_config_param('clarolineVersion'), $update_from_version))
	{
		echo '<b>Error!</b> Dokeos '.implode('|', $update_from_version).' has not been found.<br /><br />
										Please go back to step 1.
									    <p><input type="submit" name="step1" value="&lt; Back" /></p>
									    </td></tr></table></form></body></html>';

		exit ();
	}

	$dbGlu = get_config_param('dbGlu');

	if ($singleDbForm)
	{
		$courseTablePrefix = get_config_param('courseTablePrefix');
	}

	$dbScormForm = eregi_replace('[^a-z0-9_-]', '', $dbScormForm);

	if (!empty ($dbPrefixForm) && !ereg('^'.$dbPrefixForm, $dbScormForm))
	{
		$dbScormForm = $dbPrefixForm.$dbScormForm;
	}

	if (empty ($dbScormForm) || $dbScormForm == 'mysql' || $dbScormForm == $dbPrefixForm)
	{
		$dbScormForm = $dbPrefixForm.'scorm';
	}
	@ mysql_connect($dbHostForm, $dbUsernameForm, $dbPassForm);

	if (mysql_errno() > 0)
	{
		$no = mysql_errno();
		$msg = mysql_error();

		echo '<hr />['.$no.'] &ndash; '.$msg.'<hr />
										The MySQL server doesn\'t work or login / pass is bad.<br /><br />
										Please check these values:<br /><br />
									    <b>host</b> : '.$dbHostForm.'<br />
										<b>user</b> : '.$dbUsernameForm.'<br />
										<b>password</b> : '.$dbPassForm.'<br /><br />
										Please go back to step '. (defined('DOKEOS_INSTALL') ? '3' : '1').'.
									    <p><input type="submit" name="step'. (defined('DOKEOS_INSTALL') ? '3' : '1').'" value="&lt; Back" /></p>
									    </td></tr></table></form></body></html>';

		exit ();
	}

	/*
	-----------------------------------------------------------
		Normal upgrade procedure:
		start by updating main, statistic, scorm, user databases
	-----------------------------------------------------------
	*/
	if (defined('DOKEOS_INSTALL'))
	{
		//first priority: get this to work
		upgrade_v16_to_v17($dbNameForm, $dbStatsForm);
		//second priority: allow upgrading from older versions to 1.7
		//upgrade_v15_to_v16($dbNameForm);
	}
}
else
{
	echo 'You are not allowed here !';
}
?>