<?php
// $Id$
/*
============================================================================== 
	Dokeos - elearning and course management software
	
	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) various contributors
	Copyright (c) Bart Mollet
	
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
* This script shows the group space for one specific group, possibly displaying
* a list of users in the group, subscribe or unsubscribe option, tutors...
*
* @package dokeos.group
* @todo	Display error message if no group ID specified
============================================================================== 
*/
/*
============================================================================== 
		INIT SECTION
============================================================================== 
*/
$langFile = "group";
include ('../inc/claro_init_global.inc.php');
/*
-----------------------------------------------------------
	Libraries & config files
-----------------------------------------------------------
*/
include_once (api_get_library_path()."/course.lib.php");
include_once (api_get_library_path()."/groupmanager.lib.php");
/*
============================================================================== 
		MAIN CODE
============================================================================== 
*/
$current_group = GroupManager :: get_group_properties($_SESSION['_gid']);
if(!is_array($current_group) ) { 
//display some error message 
}

$nameTools = get_lang("GroupSpace");
$interbredcrump[] = array ("url" => "group.php", "name" => get_lang("GroupManagement"));

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/
if ($origin != 'learnpath')
{
	Display::display_header($nameTools,"Group");
	
	$is_allowed_to_edit = api_is_allowed_to_edit();
	api_display_tool_title($nameTools);
}
else
{
?> <link rel="stylesheet" type="text/css" href="<?php echo api_get_path(WEB_CODE_PATH); ?>css/default.css" /> <?php

}
if( isset($_GET['action']))
{
	switch( $_GET['action'])
	{
		case 'show_msg':
			Display::display_normal_message($_GET['msg']);
			break;	
	}	
}
/*
 * User wants to register in this group
 */
if ($_GET['selfReg'] && GroupManager :: is_self_registration_allowed($_SESSION['_uid'], $current_group['id']))
{
	GroupManager :: subscribe_users($_SESSION['_uid'], $current_group['id']);
	Display :: display_normal_message(get_lang('GroupNowMember'));
}
/*
 * User wants to unregister from this group
 */
if ($_GET['selfUnReg'] && GroupManager :: is_self_unregistration_allowed($_SESSION['_uid'], $current_group['id']))
{
	GroupManager :: unsubscribe_users($_SESSION['_uid'], $current_group['id']);
	Display::display_normal_message(get_lang('StudentDeletesHimself'));
}
/*
 * Show group information
 */
$course_code = $_course['sysCode'];
$is_course_member = CourseManager :: is_user_subscribed_in_real_or_linked_course($_SESSION['_uid'], $course_code);
if ($is_allowed_to_edit)
{
	echo "<a href=\"group_edit.php?origin=$origin\">".get_lang("EditGroup")."</a><br/><br/>";
}
if (GroupManager :: is_self_registration_allowed($_SESSION['_uid'], $current_group['id']))
{
	echo '<p align="right"><a href="'.$_SERVER['PHP_SELF'].'?selfReg=1&amp;group_id='.$current_group['id'].'" onclick="javascript:if(!confirm('."'".addslashes(htmlentities(get_lang("ConfirmYourChoice")))."'".')) return false;">'.get_lang("RegIntoGroup").'</a></p>';
}
if (GroupManager :: is_self_unregistration_allowed($_SESSION['_uid'], $current_group['id']))
{
	echo '<p align="right"><a href="'.$_SERVER['PHP_SELF'].'?selfUnReg=1" onclick="javascript:if(!confirm('."'".addslashes(htmlentities(get_lang("ConfirmYourChoice")))."'".')) return false;">'.get_lang("StudentUnsubscribe").'</a></p>';
}

echo '<b>'.get_lang("GroupName").':</b><blockquote>'.$current_group['name'].'</blockquote>';
if ($current_group['tutor_id'] == 0)
{
	$tutor_info = get_lang("GroupNoTutor");
}
else
{
	$tutor = api_get_user_info($current_group['tutor_id']);
	$tutor_info = '<a href="../user/userInfo.php?origin='.$origin.'&amp;uInfo='.$tutor['user_id'].'">'.$tutor['firstName']." ".$tutor['lastName'].'</a>';
}
echo '<b>'.get_lang("GroupTutor").':</b><blockquote>'.$tutor_info.'</blockquote>';
if (strlen(trim($current_group['description'])) == 0)
{
	$description = get_lang("GroupNone");
}
else
{
	$description = $current_group['description'];
}
echo '<b>'.get_lang("GroupDescription").':</b><blockquote>'.$description.'</blockquote>';
if (GroupManager :: is_subscribed($_SESSION['_uid'], $current_group['id']) || GroupManager :: is_tutor($_SESSION['_uid']))
{
	$tools = '';
	// Edited by Patrick Cool, 12 feb 2004: hide the forum link if there is no forum for this group (deleted through forum_admin.php)
	if (!is_null($current_group['forum_id']) && $current_group['forum_state'] != TOOL_NOT_AVAILABLE)
	{
		$tools .= "- <a href=\"../phpbb/viewforum.php?".api_get_cidreq()."&amp;origin=$origin&amp;gidReq=".$current_group['id']."&amp;forum=".$current_group['forum_id']."\">".get_lang("Forums")."</a><br/>";
	}
	if( $current_group['doc_state'] != TOOL_NOT_AVAILABLE )
	{
		// link to the documents area of this group
		$tools .= "- <a href=\"../document/document.php?".api_get_cidreq()."&amp;gidReq=".$current_group['id']."\">".get_lang("Documents")."</a><br/>";
	}
	echo '<b>'.get_lang("Tools").':</b><blockquote>'.$tools.'</blockquote>';

}
else
{
	$tools = '';
	if ($current_group['forum_state'] == TOOL_PUBLIC && !is_null($current_group['forum_id']))
	{
		$tools .= "- <a href=\"../phpbb/viewforum.php?".api_get_cidreq()."&amp;origin=$origin&amp;gidReq=".$current_group['id']."&amp;forum=".$current_group['forum_id']."\">".get_lang("Forums")."</a><br/>";
	}
	if( $current_group['doc_state'] == TOOL_PUBLIC )
	{
		// link to the documents area of this group
		$tools .= "- <a href=\"../document/document.php?".api_get_cidreq()."&amp;gidReq=".$current_group['id']."&amp;origin=$origin\">".get_lang("Documents")."</a><br/>";
	}
	echo '<b>'.get_lang("GroupTools").':</b><blockquote>'.$tools.'</blockquote>';
}
// list all the members of the current group
$members = GroupManager::get_subscribed_users($current_group['id']);
if (count($members) == 0)
{
	$member_info = get_lang("GroupNoneMasc");
}
else
{
	foreach($members as $index => $member)
	{
		$member_info .= "<li><a href='../user/userInfo.php?origin=".$origin."&amp;uInfo=".$member['user_id']."'>".$member['firstname']." ".$member['lastname']."</a></li>";
	}
}
echo '<b>'.get_lang("GroupMembers").':</b><blockquote><ol>'.$member_info.'</ol></blockquote>';
/*
============================================================================== 
		FOOTER 
============================================================================== 
*/
if ($origin != 'learnpath')
{
	Display::display_footer();
}
?>