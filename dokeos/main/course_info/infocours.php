<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Hugues Peeters
	Copyright (c) Roan Embrechts (Vrije Universiteit Brussel)
	Copyright (c) Olivier Brouckaert
	Copyright (c) Bart Mollet, Hogeschool Gent

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	Code to display the course settings form (for the course admin)
*	and activate the changes.
*
*	See ./inc/conf/course_info.conf.php for settings
* @todo: Move $canBeEmpty from course_info.conf.php to config-settings
* @todo: Take those config settings into account in this script
* @author Patrick Cool <patrick.cool@UGent.be>
* @author Roan Embrechts, refactoring
* and improved course visibility|subscribe|unsubscribe options
* @package dokeos.course_info
==============================================================================
*/
/*
==============================================================================
	   INIT SECTION
==============================================================================
*/
api_use_lang_files('create_course', 'course_info');
include ('../inc/claro_init_global.inc.php');
$this_section = SECTION_COURSES;

// Roles and rights system
$user_id = api_get_user_id();
$course_id = api_get_course_id();
$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
$location_id = RolesRights::get_course_tool_location_id($course_id, TOOL_COURSE_SETTING);
$is_allowed = RolesRights::is_allowed_which_rights($role_id, $location_id);

//block users without view right
RolesRights::protect_location($role_id, $location_id);

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/
require_once (api_get_library_path()."/course.lib.php");
require_once (api_get_include_path()."/conf/course_info.conf.php");
require_once (api_get_include_path()."/lib/text.lib.php");
require_once (api_get_include_path()."/lib/debug.lib.inc.php");
require_once (api_get_library_path().'/formvalidator/FormValidator.class.php');
/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
define("MODULE_HELP_NAME", "Settings");
define("COURSE_CHANGE_PROPERTIES", "COURSE_CHANGE_PROPERTIES");
$TABLECOURSE = Database :: get_main_table(MAIN_COURSE_TABLE);
$TABLEFACULTY = Database :: get_main_table(MAIN_CATEGORY_TABLE);
$TABLECOURSEHOME = Database :: get_course_tool_list_table();
$TABLELANGUAGES = Database :: get_main_table(MAIN_LANGUAGE_TABLE);
$TABLEBBCONFIG = Database :: get_course_table(FORUM_CONFIG_TABLE);
$currentCourseID = $_course['sysCode'];
$currentCourseRepository = $_course["path"];
$is_allowedToEdit = $is_courseAdmin;

/*
==============================================================================
		LOGIC FUNCTIONS
==============================================================================
*/
function is_settings_editable()
{
	return $GLOBALS["course_info_is_editable"];
}
$course_code = $_course["sysCode"];
$course_access_settings = CourseManager :: get_access_settings($course_code);

/**
* Changes are made in the role-right-location table,
* according to the old visibility level and the new one.
*
* ---- General course view right ----
* open to the world:
* - all local roles get view right on course level.
* open to the platform:
* - all local roles up to registered guest get view right;
* - anonymous guest does not get view right.
* only for registered users:
* - all local roles up to normal course members get view right;
* - guest roles does not get view right.
* closed:
* - only course admin and teachign assistant get view right.
*
* ---- Course tools ----
* When moving from open for the platform to open for the world:
* - the view right for all course tools for anonymous guests
* is set to be the same as the rights for registered guests
* When moving from only for registered users to open for the world:
* - the view right for all course tools for anonymous guests and registered
* guests is set to be the same as the rights for normal course members
* When moving from only for registered users to open for the platform:
* - the view right for all course tools for registered guests
* is set to be the same as the rights for normal course members
*
* @todo also close course tools access again when lowering visibility level
*/
function adjust_view_rights($visibility_level, $old_access_settings)
{
	$course_code = api_get_course_id();
	$course_location_id = RolesRights::get_course_location_id($course_code);
	$old_visibility_level = $old_access_settings['visibility'];

	if ($visibility_level == COURSE_VISIBILITY_OPEN_WORLD)
	{
		//general course view right changes
		RolesRights::set_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, true);
		RolesRights::set_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, true);
		RolesRights::set_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $course_location_id, true);
		RolesRights::set_value(TEACHING_ASSISTANT, VIEW_RIGHT, $course_location_id, true);
		RolesRights::set_value(COURSE_ADMIN, VIEW_RIGHT, $course_location_id, true);
		
		//course tool view right changes
		if ($old_visibility_level == COURSE_VISIBILITY_OPEN_PLATFORM)
		{
			//the view right for the anonymous visitors for all course tools
			//is made equal to the view right for registered guest
			$short_location = RolesRights::get_short_course_location_path($course_code);
			RolesRights::copy_sublocation_values_role_to_role(REGISTERED_GUEST_COURSE_VISITOR, ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $short_location);
		}
		else if ($old_visibility_level == COURSE_VISIBILITY_REGISTERED)
		{
			//the view right for the anonymous visitors and registered guests for all course tools
			//is made equal to the view right for normal course members
			$short_location = RolesRights::get_short_course_location_path($course_code);
			RolesRights::copy_sublocation_values_role_to_role(NORMAL_COURSE_MEMBER, REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $short_location);
			RolesRights::copy_sublocation_values_role_to_role(NORMAL_COURSE_MEMBER, ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $short_location);
		}
	}
	else if ($visibility_level == COURSE_VISIBILITY_OPEN_PLATFORM)
	{
		//general course view right changes
		RolesRights::set_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, false);
		RolesRights::set_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, true);
		RolesRights::set_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $course_location_id, true);
		RolesRights::set_value(TEACHING_ASSISTANT, VIEW_RIGHT, $course_location_id, true);
		RolesRights::set_value(COURSE_ADMIN, VIEW_RIGHT, $course_location_id, true);
		
		if ($old_visibility_level == COURSE_VISIBILITY_REGISTERED)
		{
			//the view right for the registered guests for all course tools
			//is made equal to the view right for normal course members
			$short_location = RolesRights::get_short_course_location_path($course_code);
			RolesRights::copy_sublocation_values_role_to_role(NORMAL_COURSE_MEMBER, REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $short_location);
		}
		else if ($old_visibility_level == COURSE_VISIBILITY_OPEN_WORLD)
		{
			//set view right for all sublocations of course to false for the ANONYMOUS_GUEST_COURSE_VISITOR
		}
	}
	else if ($visibility_level == COURSE_VISIBILITY_REGISTERED)
	{
		//general course view right changes
		RolesRights::set_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, false);
		RolesRights::set_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, false);
		RolesRights::set_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $course_location_id, true);
		RolesRights::set_value(TEACHING_ASSISTANT, VIEW_RIGHT, $course_location_id, true);
		RolesRights::set_value(COURSE_ADMIN, VIEW_RIGHT, $course_location_id, true);
	}
	else if ($visibility_level == COURSE_VISIBILITY_CLOSED)
	{
		//general course view right changes
		RolesRights::set_value(ANONYMOUS_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, false);
		RolesRights::set_value(REGISTERED_GUEST_COURSE_VISITOR, VIEW_RIGHT, $course_location_id, false);
		RolesRights::set_value(NORMAL_COURSE_MEMBER, VIEW_RIGHT, $course_location_id, false);
		RolesRights::set_value(TEACHING_ASSISTANT, VIEW_RIGHT, $course_location_id, true);
		RolesRights::set_value(COURSE_ADMIN, VIEW_RIGHT, $course_location_id, true);
	}
}

/*
==============================================================================
		MAIN CODE
==============================================================================
*/

if (!$is_allowedToEdit)
{
	api_not_allowed();
}
// Get all course categories
$table_course_category = Database :: get_main_table(MAIN_CATEGORY_TABLE);
$sql = "SELECT code,name FROM ".$table_course_category." WHERE auth_course_child ='TRUE'  OR code = '".mysql_real_escape_string($_course['categoryCode'])."'  ORDER BY tree_pos";
$res = api_sql_query($sql, __FILE__, __LINE__);
while ($cat = mysql_fetch_array($res))
{
	$categories[$cat['code']] = '('.$cat['code'].') '.$cat['name'];
}
// Build the form
$form = new FormValidator('update_course');
$form->add_textfield('visual_code', get_lang('Code'));
$form->applyFilter('visual_code', 'strtoupper');
$form->add_textfield('tutor_name', get_lang('Professors'), true, array ('size' => '60'));
$form->add_textfield('title', get_lang('Title'), true, array ('size' => '60'));
$form->addElement('select', 'category_code', get_lang('Fac'), $categories);
$form->add_textfield('department_name', get_lang('Department'), false, array ('size' => '60'));
$form->add_textfield('department_url', get_lang('DepartmentUrl'), false, array ('size' => '60'));
$form->addRule('tutor_name', get_lang('ThisFieldIsRequired'), 'required');
$form->addElement('select_language', 'course_language', get_lang('Ln'));
$form->addElement('static', null, null, get_lang("TipLang"));
$form->addElement('radio', 'visibility', get_lang("CourseAccess"), get_lang('OpenToTheWorld'), COURSE_VISIBILITY_OPEN_WORLD);
$form->addElement('radio', 'visibility', null, get_lang('OpenToThePlatform'), COURSE_VISIBILITY_OPEN_PLATFORM);
$form->addElement('radio', 'visibility', null, get_lang('Private'), COURSE_VISIBILITY_REGISTERED);
$form->addElement('radio', 'visibility', null, get_lang('CourseVisibilityClosed'), COURSE_VISIBILITY_CLOSED);
$form->addElement('radio', 'visibility', null, get_lang('CourseVisibilityModified'), COURSE_VISIBILITY_MODIFIED);
$form->addElement('radio', 'subscribe', get_lang('Subscription'), get_lang('Allowed'), 1);
$form->addElement('radio', 'subscribe', null, get_lang('Denied'), 0);
$form->addElement('radio', 'unsubscribe', get_lang('Unsubscription'), get_lang('AllowedToUnsubscribe'), 1);
$form->addElement('radio', 'unsubscribe', null, get_lang('NotAllowedToUnsubscribe'), 0);
$form->addElement('static', null, null, get_lang("ConfTip"));
if (is_settings_editable())
{
	$form->addElement('submit', null, get_lang('Ok'));
}
else
{
	$form->freeze();
}
// Set the default values of the form
$values['title'] = $_course['name'];
$values['visual_code'] = $_course['official_code'];
$values['category_code'] = $_course['categoryCode'];
$values['tutor_name'] = $_course['titular'];
$values['course_language'] = $_course['language'];
$values['department_name'] = $_course['extLink']['name'];
$values['department_url'] = $_course['extLink']['url'];
$values['visibility'] = $_course['visibility'];
$values['subscribe'] = $course_access_settings['subscribe'];
$values['unsubscribe'] = $course_access_settings['unsubscribe'];
$form->setDefaults($values);
// Validate form
if ($form->validate() && is_settings_editable())
{
	$update_values = $form->exportValues();
	foreach ($update_values as $index => $value)
	{
		$update_values[$index] = mysql_real_escape_string($value);
	}
	$table_course = Database :: get_main_table(MAIN_COURSE_TABLE);
	$old_access_settings = CourseManager::get_access_settings($course_code);
	$sql = "UPDATE $table_course SET title 			= '".$update_values['title']."',
										 visual_code 	= '".$update_values['visual_code']."',
										 course_language = '".$update_values['course_language']."',
										 category_code  = '".$update_values['category_code']."',
										 department_name  = '".$update_values['department_name']."',
										 department_url  = '".$update_values['department_url']."',
										 visibility  = '".$update_values['visibility']."',
										 subscribe  = '".$update_values['subscribe']."',
										 unsubscribe  = '".$update_values['unsubscribe']."',
										 tutor_name     = '".$update_values['tutor_name']."' 
									WHERE code = '".$course_code."'";
	api_sql_query($sql, __FILE__, __LINE__);
	api_sql_query("UPDATE ".$TABLEBBCONFIG." SET default_lang='".$update_values['course_language']."'", __FILE__, __LINE__);
	adjust_view_rights($update_values['visibility'], $old_access_settings);
	$cidReset = true;
	$cidReq = $course_code;
	include ('../inc/claro_init_local.inc.php');
	header('Location: infocours.php?action=show_message&amp;cidReq='.$course_code);
	exit;
}
/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/
$nameTools = get_lang("ModifInfo");
Display :: display_header($nameTools, MODULE_HELP_NAME);
api_display_tool_title($nameTools);
if (isset ($_GET['action']) && $_GET['action'] == 'show_message')
{
	Display :: display_normal_message(get_lang('ModifDone'));
}
// Display the form
$form->display();
if ($showDiskQuota && $currentCourseDiskQuota != "")
{
?>
<table>
	<tr>
	<td><?php echo get_lang("DiskQuota"); ?>&nbsp;:</td>
	<td><?php echo $currentCourseDiskQuota; ?> <?php echo $byteUnits[0] ?></td>
	</tr>
	<?php

}
if ($showLastEdit && $currentCourseLastEdit != "" && $currentCourseLastEdit != "0000-00-00 00:00:00")
{
?>
	<tr>
	<td><?php echo get_lang('LastEdit'); ?>&nbsp;:</td>
	<td><?php echo format_locale_date($dateTimeFormatLong,strtotime($currentCourseLastEdit)); ?></td>
	</tr>
	<?php

}
if ($showLastVisit && $currentCourseLastVisit != "" && $currentCourseLastVisit != "0000-00-00 00:00:00")
{
?>
	<tr>
	<td><?php echo get_lang('LastVisit'); ?>&nbsp;:</td>
	<td><?php echo format_locale_date($dateTimeFormatLong,strtotime($currentCourseLastVisit)); ?></td>
	</tr>
	<?php

}
if ($showCreationDate && $currentCourseCreationDate != "" && $currentCourseCreationDate != "0000-00-00 00:00:00")
{
?>
	<tr>
	<td><?php echo get_lang('CreationDate'); ?>&nbsp;:</td>
	<td><?php echo format_locale_date($dateTimeFormatLong,strtotime($currentCourseCreationDate)); ?></td>
	</tr>
	<?php

}
if ($showExpirationDate && $currentCourseExpirationDate != "" && $currentCourseExpirationDate != "0000-00-00 00:00:00")
{
?>
	<tr>
	<td><?php echo get_lang('ExpirationDate'); ?>&nbsp;:</td>
	<td>
	<?php

	echo format_locale_date($dateTimeFormatLong, strtotime($currentCourseExpirationDate));
	echo "<BR>Soit dans : ";
	$nbJour = (strtotime($currentCourseExpirationDate) - time()) / (60 * 60 * 24);
	$nbAnnees = round($nbJour / 365);
	$nbJour = round($nbJour - $nbAnnees * 365);
	switch ($nbAnnees)
	{
		case "1" :
			echo $nbAnnees, " an ";
			break;
		case "0" :
			break;
		default :
			echo $nbAnnees, " ans ";
	};
	switch ($nbJour)
	{
		case "1" :
			echo $nbJour, " jour ";
			break;
		case "0" :
			break;
		default :
			echo $nbJour, " jours ";
	}
	if ($canReportExpirationDate)
	{
		echo " -&gt; <a href=\"".$urlScriptToReportExpirationDate."\">".get_lang('PostPone')."</a>";
	}
?>
</td>
</tr>
</table>
<?php

}

echo "<hr noshade size=\"1\"/>";
echo "<a href=\"course_rights.php\">".get_lang("OverviewCourseRights")."</a> | ";

if ($showLinkToDeleteThisCourse)
{
	?>
		<a href="delete_course.php"><?php echo get_lang("DelCourse"); ?></a>
	<?php
	if ($showLinkToBackupThisCourse || $showLinkToRecycleThisCourse)
	{
		echo '|';
	}
}
if ($showLinkToBackupThisCourse)
{
	?>
		<a href="../coursecopy/backup.php"><?php echo get_lang("backup"); ?></a>
	<?php
	if ($showLinkToRecycleThisCourse)
	{
		echo '|';
	}
}
if ($showLinkToRecycleThisCourse)
{
	?>
		<a href="../coursecopy/recycle_course.php"><?php echo get_lang("recycle_course"); ?></a>
	<?php
}
/*
==============================================================================
		FOOTER
==============================================================================
*/
Display :: display_footer();
?>