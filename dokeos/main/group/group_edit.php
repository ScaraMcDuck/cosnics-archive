<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) various contributors
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
*	This script displays an area where teachers can edit the group properties and member list.
*	Groups are also often called "teams" in the Dokeos code.
*
*	@author various contributors
*	@author Roan Embrechts (VUB), partial code cleanup, initial virtual course support
	@package dokeos.group
*	@todo course admin functionality to create groups based on who is in which course (or class).
==============================================================================
*/
/*
==============================================================================
		INIT SECTION
==============================================================================
*/
api_use_lang_files("group");
include ('../inc/claro_init_global.inc.php');
$this_section = SECTION_COURSES;

/*
-----------------------------------------------------------
	Libraries & settings
-----------------------------------------------------------
*/
require_once (api_get_library_path().'/course.lib.php');
require_once (api_get_library_path().'/groupmanager.lib.php');
require_once (api_get_library_path().'/formvalidator/FormValidator.class.php');
/*
-----------------------------------------------------------
	Constants & variables
-----------------------------------------------------------
*/
$current_group = GroupManager :: get_group_properties($_SESSION['_gid']);
/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/
$nameTools = get_lang('EditGroup');
$interbredcrump[] = array ("url" => "group.php", "name" => get_lang('GroupManagement'));

if (!api_is_allowed_to_edit())
{
	api_not_allowed();
}
/*
==============================================================================
		FUNCTIONS
==============================================================================
*/
/**
 * Function to check the given max number of members per group
 */
function check_max_number_of_members($value)
{
	$max_member_no_limit = $value['max_member_no_limit'];
	if ($max_member_no_limit == MEMBER_PER_GROUP_NO_LIMIT)
	{
		return true;
	}
	$max_member = $value['max_member'];
	return is_numeric($max_member);
}
/**
 * Function to check if the number of selected group members is valid
 */
function check_group_members($value)
{
	if ($value['max_member_no_limit'] == MEMBER_PER_GROUP_NO_LIMIT)
	{
		return true;
	}
	if ($value['max_member'] < count($value['group_members']))
	{
		return array ('group_members' => get_lang('GroupTooMuchMembers'));
	}
	return true;
}
/*
==============================================================================
		MAIN CODE
==============================================================================
*/

// Build form
$form = new FormValidator('group_edit');
$form->addElement('hidden', 'action');
$form->addElement('hidden', 'referer');
$form->addElement('hidden', 'forum_id');
// Group name
$form->add_textfield('name', get_lang('GroupName'));
// Description
$form->addElement('textarea', 'description', get_lang('GroupDescription'), array ('cols' => 50, 'rows' => 6));
// Tutors
$tutors = GroupManager :: get_all_tutors();
$possible_tutors[0] = get_lang('GroupNoTutor');
foreach ($tutors as $index => $tutor)
{
	$possible_tutors[$tutor['user_id']] = $tutor['lastname'].' '.$tutor['firstname'];
}
$group = array ();
$group[] = & $form->createElement('select', 'tutor_id', null, $possible_tutors);
$group[] = & $form->createElement('static', null, null, '&nbsp;&nbsp;<a href="../user/user.php">'.get_lang('AddTutors').'</a>');
$form->addGroup($group, 'tutor_group', get_lang('GroupTutor'), '', false);
// Members per group
$form->addElement('radio', 'max_member_no_limit', get_lang('GroupLimit'), get_lang('NoLimit'), MEMBER_PER_GROUP_NO_LIMIT);
$group = array ();
$group[] = & $form->createElement('radio', 'max_member_no_limit', null, get_lang('Max'), 1);
$group[] = & $form->createElement('text', 'max_member', null, array ('size' => 2));
$group[] = & $form->createElement('static', null, null, get_lang('GroupPlacesThis'));
$form->addGroup($group, 'max_member_group', null, '', false);
$form->addRule('max_member_group', get_lang('InvalidMaxNumberOfMembers'), 'callback', 'check_max_number_of_members');
// Self registration
$form->addElement('checkbox', 'self_registration_allowed', get_lang('GroupSelfRegistration'), get_lang('GroupAllowStudentRegistration'), 1);
$form->addElement('checkbox', 'self_unregistration_allowed', null, get_lang('GroupAllowStudentUnregistration'), 1);
// Forum settings
$form->addElement('radio', 'forum_state', get_lang('GroupForum'), get_lang('NotAvailable'), TOOL_NOT_AVAILABLE);
$form->addElement('radio', 'forum_state', null, get_lang('Public'), TOOL_PUBLIC);
$form->addElement('radio', 'forum_state', null, get_lang('Private'), TOOL_PRIVATE);
// Documents settings
$form->addElement('radio', 'doc_state', get_lang('GroupDocument'), get_lang('NotAvailable'), TOOL_NOT_AVAILABLE);
$form->addElement('radio', 'doc_state', null, get_lang('Public'), TOOL_PUBLIC);
$form->addElement('radio', 'doc_state', null, get_lang('Private'), TOOL_PRIVATE);
// Group members
$complete_user_list = GroupManager :: get_complete_list_of_users_that_can_be_added_to_group($course_id, $current_group['id']);
$possible_users = array ();
foreach ($complete_user_list as $index => $user)
{
	$possible_users[$user['user_id']] = $user['lastname'].' '.$user['firstname'];
}
$group_member_list = GroupManager :: get_subscribed_users($current_group['id']);
$selected_users = array ();
foreach ($group_member_list as $index => $user)
{
	$possible_users[$user['user_id']] = $user['lastname'].' '.$user['firstname'];
	$selected_users[] = $user['user_id'];
}
$group_members_element = $form->addElement('advmultiselect', 'group_members', get_lang('GroupMembers'), $possible_users);
$group_members_element->setElementTemplate('
{javascript}
<table{class}>
<!-- BEGIN label_2 --><tr><th>{label_2}</th><!-- END label_2 -->
<!-- BEGIN label_3 --><th>&nbsp;</th><th>{label_3}</th></tr><!-- END label_3 -->
<tr>
  <td valign="top">{unselected}</td>
  <td align="center">{add}<br /><br />{remove}</td>
  <td valign="top">{selected}</td>
</tr>
</table>
');
$form->addFormRule('check_group_members');
$form->addElement('submit', 'submit', get_lang('Ok'));
if ($form->validate())
{
	$values = $form->exportValues();
	if ($values['max_member_no_limit'] == MEMBER_PER_GROUP_NO_LIMIT)
	{
		$max_member = MEMBER_PER_GROUP_NO_LIMIT;
	}
	else
	{
		$max_member = $values['max_member'];
	}
	$self_registration_allowed = isset ($values['self_registration_allowed']) ? 1 : 0;
	$self_unregistration_allowed = isset ($values['self_unregistration_allowed']) ? 1 : 0;
	GroupManager :: set_group_properties($current_group['id'], strip_tags($values['name']), strip_tags($values['description']), $values['tutor_id'], $max_member, $values['forum_id'], $values['forum_state'], $values['doc_state'], $self_registration_allowed, $self_unregistration_allowed);
	GroupManager :: unsubscribe_all_users($current_group['id']);
	if (isset ($_POST['group_members']) && count($_POST['group_members']) > 0)
	{
		GroupManager :: subscribe_users($values['group_members'], $current_group['id']);
	}
	$cat = GroupManager :: get_category_from_group($current_group['id']);
	header('Location: '.$values['referer'].'?action=show_msg&msg='.get_lang('GroupSettingsModified').'&category='.$cat['id']);
}
$defaults = $current_group;
$defaults['group_members'] = $selected_users;
$defaults['action'] = $action;
if ($defaults['maximum_number_of_students'] == MEMBER_PER_GROUP_NO_LIMIT)
{
	$defaults['max_member_no_limit'] = MEMBER_PER_GROUP_NO_LIMIT;
}
else
{
	$defaults['max_member_no_limit'] = 1;
	$defaults['max_member'] = $defaults['maximum_number_of_students'];
}
$referer = parse_url($_SERVER['HTTP_REFERER']);
$referer = basename($referer['path']);
if ($referer != 'group_space.php' && $referer != 'group.php')
{
	$referer = 'group.php';
}
Display :: display_header($nameTools, "Group");
api_display_tool_title($nameTools);
?>
<a href="group_space.php"><?php  echo get_lang('GroupSpace') ?></a>
<br/>
<br/>
<?php
$defaults['referer'] = $referer;
$form->setDefaults($defaults);
$form->display();
/*
==============================================================================
		FOOTER
==============================================================================
*/
Display :: display_footer();
?>