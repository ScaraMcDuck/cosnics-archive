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
*	@package dokeos.group
============================================================================== 
*/
/*
============================================================================== 
		INIT SECTION
============================================================================== 
*/
api_use_lang_files("group");
require_once ('../inc/claro_init_global.inc.php');
$this_section = SECTION_COURSES;
require_once (api_get_library_path().'/groupmanager.lib.php');
require_once (api_get_library_path().'/debug.lib.inc.php');
require_once (api_get_library_path().'/formvalidator/FormValidator.class.php');
if (!api_is_allowed_to_edit() || !(isset ($_GET['id']) || isset ($_POST['id']) || isset ($_GET['action']) || isset ($_POST['action'])))
{
	api_not_allowed();
}
/**
 * Function to check the given max number of members per group 
 */
function check_max_number_of_members($value)
{
	$max_member_no_limit = $value['max_member_no_limit'];
	if( $max_member_no_limit == MEMBER_PER_GROUP_NO_LIMIT)
	{
		return true;	
	}
	$max_member = $value['max_member'];
	return is_numeric($max_member);
}
/**
 * Function to check the number of groups per user
 */
function check_groups_per_user($value)
{
	$groups_per_user = $value['groups_per_user'];
	if(isset ($_POST['id']) && intval($groups_per_user) != GROUP_PER_MEMBER_NO_LIMIT && GroupManager :: get_current_max_groups_per_user($_POST['id']) > intval($groups_per_user))
	{	
		return false;			
	}
	return true;
}

if (get_setting('allow_group_categories') == 'true')
{
	if (isset ($_GET['id']))
	{
		$category = GroupManager :: get_category($_GET['id']);
		$nameTools = get_lang('Edit').' '.$category['title'];
	}
	else
	{
		$nameTools = get_lang('AddCategory');
		// default values for new category
		$category = array ('groups_per_user' => 1, 'forum_state' => TOOL_PRIVATE, 'doc_state' => TOOL_PRIVATE, 'max_student' => 0);
	}
}
else
{
	$category = GroupManager :: get_category($_GET['id']);
	$nameTools = get_lang('PropModify');
}
$interbredcrump[] = array ("url" => "group.php", "name" => get_lang('GroupManagement'));
// Build the form
if (isset ($_GET['id']))
{
	// Update settings of existing category
	$action = 'update_settings';
	$form = new FormValidator('group_category', 'post', '?id='.$category['id']);
	$form->addElement('hidden', 'id');
}
else
{
	// Create a new category
	$action = 'add_category';
	$form = new FormValidator('group_category');
}
// If categories allowed, show title & description field
if (get_setting('allow_group_categories') == 'true')
{
	$form->add_textfield('title', get_lang('Title'));
	$form->addElement('textarea', 'description', get_lang('Description'),array('cols'=>50,'rows'=>6));
}
else
{
	$form->addElement('hidden','title');
	$form->addElement('hidden','description');	
}
// Action
$form->addElement('hidden', 'action');
// Groups per user
$group = array ();
$group[] = & $form->createElement('static', null, null, get_lang('QtyOfUserCanSubscribe_PartBeforeNumber'));
$possible_values = array ();
for ($i = 1; $i <= 10; $i ++)
{
	$possible_values[$i] = $i;
}
$possible_values[GROUP_PER_MEMBER_NO_LIMIT] = get_lang('All');
$group[] = & $form->createElement('select', 'groups_per_user', null, $possible_values);
$group[] = & $form->createElement('static', null, null, get_lang('QtyOfUserCanSubscribe_PartAfterNumber'));
$form->addGroup($group, 'limit_group', get_lang('GroupLimit'), ' ', false);
$form->addRule('limit_group',get_lang('MaxGroupsPerUserInvalid'),'callback','check_groups_per_user');
// Default settings for new groups
$form->addElement('header', null, get_lang('DefaultSettingsForNewGroups'));
// Members per group
$form->addElement('radio', 'max_member_no_limit', get_lang('GroupLimit'), get_lang('NoLimit'),MEMBER_PER_GROUP_NO_LIMIT);
$group = array ();
$group[] = & $form->createElement('radio', 'max_member_no_limit',null,get_lang('Max'),1);
$group[] = & $form->createElement('text', 'max_member', null, array ('size' => 2));
$group[] = & $form->createElement('static', null, null, get_lang('GroupPlacesThis'));
$form->addGroup($group, 'max_member_group', null, '',false);
$form->addRule('max_member_group',get_lang('InvalidMaxNumberOfMembers'),'callback','check_max_number_of_members');
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
// Submit
$form->addElement('submit', 'submit', get_lang('Ok'));
// If form validates -> save data
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
	$self_reg_allowed = isset ($values['self_registration_allowed']) ? $values['self_registration_allowed'] : 0;
	$self_unreg_allowed = isset ($values['self_unregistration_allowed']) ? $values['self_unregistration_allowed'] : 0;
	switch ($values['action'])
	{
		case 'update_settings' :
			GroupManager :: update_category($values['id'], $values['title'], $values['description'], $values['forum_state'], $values['doc_state'], $self_reg_allowed, $self_unreg_allowed, $max_member, $values['groups_per_user']);
			$msg = urlencode(get_lang("GroupPropertiesModified"));
			header('Location: group.php?action=show_msg&msg='.$msg.'&category='.$values['id']);
			break;
		case 'add_category' :
			GroupManager :: create_category($values['title'], $values['description'], $values['forum_state'], $values['doc_state'], $self_reg_allowed, $self_unreg_allowed, $max_member, $values['groups_per_user']);
			$msg = urlencode(get_lang("CategoryCreated"));
			header('Location: group.php?action=show_msg&msg='.$msg);
			break;
	}
}
// Else display the form
Display :: display_header($nameTools, "Group");
api_display_tool_title($nameTools);
$defaults = $category;
$defaults['action'] = $action;
if( $defaults['max_student'] == MEMBER_PER_GROUP_NO_LIMIT)
{
	$defaults['max_member_no_limit'] = MEMBER_PER_GROUP_NO_LIMIT;
}
else
{
	$defaults['max_member_no_limit'] = 1;	
	$defaults['max_member'] = $defaults['max_student'];
}
$form->setDefaults($defaults);
$form->display();
Display :: display_footer();
?>