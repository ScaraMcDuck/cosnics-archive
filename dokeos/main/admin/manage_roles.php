<?php // $Id$ 
/*
============================================================================== 
	Dokeos - elearning and course management software
	
	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) Roan Embrechts, Vrije Universiteit Brussel
	
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
* This script contains code to display and change
* global and local roles.
*
* @package dokeos.admin
============================================================================== 
*/

/*
============================================================================== 
		INIT SECTION
============================================================================== 
*/ 

api_use_lang_files("admin"); 
include("../inc/global.inc.php"); 
$this_section=SECTION_PLATFORM_ADMIN;

require_once(api_get_library_path().'/formvalidator/FormValidator.class.php');

api_protect_admin_script();

/*
-----------------------------------------------------------
	Constants
-----------------------------------------------------------
*/ 
define("EDIT_ROLE", "edit_role");
define("CREATE_LOCAL_ROLE","create_new_local_role");
define("CREATE_GLOBAL_ROLE","create_new_global_role");

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/ 
$tool_name = get_lang("ManageRoles"); // title of the page (should come from the language file) 
$interbredcrump[]=array("url" => "index.php","name" => get_lang("PlatformAdmin"));
Display::display_header($tool_name);

/*
============================================================================== 
		FUNCTIONS
============================================================================== 
*/ 

/**
* Displays the list of all local roles.
* Global roles are hidden for the moment, until they're useful.
*/
function display_role_list()
{
	//has to have numerical array indices for sortable table to work well
	$roles = RolesRights::get_role_list(MYSQL_NUM);
	
	foreach ($roles as $this_role)
	{
		$edit_function = '<a href="?action='.EDIT_ROLE.'&amp;role_id='.$this_role[0].'"><img src="../img/edit.gif" border="0" style="vertical-align: middle;" title="'.get_lang('Edit').'" alt="'.get_lang('Edit').'"/></a>';
		
		$this_role[1] = RolesRights::get_visual_role_name_from_name($this_role[1]);
		$this_role[4] = RolesRights::get_visual_role_name_from_name($this_role[4]);
		$this_role[5] = $edit_function;
		$list_for_display[] = $this_role;
	}
	
	$table_header[] = array(get_lang('RoleId'),true);
	$table_header[] = array (get_lang('RoleName'), true);
	$table_header[] = array (get_lang('RoleType'), true);
	$table_header[] = array (get_lang('RoleCreatedBy'), true);
	$table_header[] = array (get_lang('RoleDescription'), true);
	$table_header[] = array ('', false);
	$sorting_options['column'] = 2;
	Display :: display_sortable_table($table_header, $list_for_display, $sorting_options);
}

function display_actions()
{
	echo '<ul><li><a href="?action='.CREATE_LOCAL_ROLE.'">' . get_lang("CreateNewLocalRole") . '</a></li>';
	echo '<li><a href="?action='.CREATE_GLOBAL_ROLE.'">' . get_lang("CreateNewGlobalRole") . '</a></li></ul>';
}

/**
* Shows a table overview for one specific role.
* For this we first create a 2D array with data, then display it
* One axis becomes the rights axis, the other the location axis.
* the elements inside the table are the values (true or false)
* for every right / location combination.
*/
function display_overview_focus_role($role_id)
{
	echo '<p><strong>Overview for role ' . RolesRights::get_visual_role_name($role_id) . '</strong></p>';
	
	$course_code = DEFAULT_COURSE;
	$course_location_part = RolesRights::get_short_course_location_path($course_code) . RolesRights::get_location_path_separator();
	
	$focus_on_role_data = RolesRights::get_focus_on_role_data($role_id, $course_code);
	$two_axis_table_data = $focus_on_role_data['raw_data'];
	$two_axis_table_data_visual = $focus_on_role_data['visual_data'];
	$x_limit = $focus_on_role_data['x_limit'];
	$y_limit = $focus_on_role_data['y_limit'];
	
	//echo data
	echo '<table class="data_table">';
	for ($y = 0; $y <= $y_limit; $y++)
	{
		if ( $y > 0)
		{
			$location = $two_axis_table_data_visual[0][$y];
			//$location_exact = RolesRights::get_location_from_location_id($two_axis_table_data[0][$y]);
			//if (! stristr($location_exact, $course_location_part)) continue;
		}
		echo '<tr>';
		for ($x = 0; $x <= $x_limit; $x++)
		{
			$value = $two_axis_table_data_visual[$x][$y];
			if ($value == 'true')
				$value_output = '<a class="role_right_true" href="?action='.EDIT_ROLE.'&amp;role_id='. $role_id . '&amp;location_id=' . $two_axis_table_data[0][$y] . '&amp;right_id='. $two_axis_table_data[$x][0] . '&amp;original_value=' . $value . '&amp;focus_role=true&amp;role_list='.$role_id.'"><font color="green">'.'<img src="../img/setting_true.gif" alt="'.get_lang('Yes').'" />'.'</font></a>';
			else if ($value == 'false')
				$value_output = '<a class="role_right_false" href="?action='.EDIT_ROLE.'&amp;role_id='. $role_id . '&amp;location_id=' . $two_axis_table_data[0][$y] . '&amp;right_id='. $two_axis_table_data[$x][0] . '&amp;original_value=' . $value . '&amp;focus_role=true&amp;role_list='.$role_id.'"><font color="red">'.'<img src="../img/setting_false.gif" alt="'.get_lang('No').'" />'.'</font></a>';
			else $value_output = $value;
			
			echo '<td>' . $value_output . '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}

function display_new_local_role_form()
{
	$role_list = RolesRights::get_role_list();
	foreach($role_list as $this_role)
	{
		$role_id = $this_role['id'];
		$role_list_form_data[$role_id] = RolesRights::get_visual_role_name($role_id);
	}
	echo get_lang('CreateNewLocalRoleInfo');
	$form = new FormValidator('create_local_role');
	$form->add_textfield('role_name', get_lang('RoleName'));
	$form->add_textfield('role_description', get_lang('RoleDescription'));
 	$form->addElement('select', 'based_on_role', get_lang('InitialRightsBasedOnLocalRole'), $role_list_form_data);
	$form->addElement('submit', 'submit_new_local_role', get_lang('Ok'));
	$form->display();
}

function display_new_global_role_form()
{
	echo get_lang('CreateNewGlobalRoleInfo');
	$form = new FormValidator('create_global_role');
	$form->add_textfield('role_name', get_lang('RoleName'));
	$form->add_textfield('role_description', get_lang('RoleDescription'));
	$form->addElement('submit', 'submit_new_global_role', get_lang('Ok'));
	$form->display();
}

/*
============================================================================== 
		MAIN SECTION
============================================================================== 
*/ 

$action = $_GET['action'];
$role_id = $_REQUEST['role_id'];
$location_id = $_REQUEST['location_id'];
$right_id = $_REQUEST['right_id'];
$original_value = $_REQUEST['original_value'];
$role_name = $_REQUEST['role_name'];
$role_description = $_REQUEST['role_description'];
$based_on_role = $_REQUEST['based_on_role'];

api_display_tool_title($tool_name);

if (isset($action) && $action == EDIT_ROLE)
{
	echo '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang('BackToOverviewOfRoles') . '</a>';
	if(isset($original_value) && $original_value)
	{
		$new_value = $original_value == "true" ? false : true;
		RolesRights::set_value($role_id, $right_id, $location_id, $new_value);
		Display::display_normal_message(get_lang("RightValueModified"));
	}
	display_overview_focus_role($role_id);
}
else if (isset($action) && $action == CREATE_LOCAL_ROLE)
{
	display_actions();
	display_new_local_role_form();
}
else if (isset($action) && $action == CREATE_GLOBAL_ROLE)
{
	display_actions();
	display_new_global_role_form();
}
else if (isset($_POST['submit_new_local_role']) && $_POST['submit_new_local_role'])
{
	RolesRights::create_local_role($role_name, $role_description, api_get_user_id(), $based_on_role);
	Display::display_normal_message(get_lang("NewLocalRoleCreated"));
	display_actions();
	display_role_list();
}
else if (isset($_POST['submit_new_global_role']) && $_POST['submit_new_global_role'])
{
	RolesRights::create_global_role($role_name, $role_description, api_get_user_id());
	Display::display_normal_message(get_lang("NewGlobalRoleCreated"));
	display_actions();
	display_role_list();
}
else
{
	display_actions();
	display_role_list();
}

/*
==============================================================================
		FOOTER 
==============================================================================
*/ 
Display::display_footer();
?>