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
* This script contains code to display an overview of global
* and local roles, rights, and locations.
*
* Currently working on editing options, basic system working when focusing on rights,
* this will extend to other parts soon.
* See also the new API library role_right.lib.php
*
* @package dokeos.admin
* @todo move several functions to new API library (role_right.lib.php ?)
* @todo ability to focus on a certain subpart of the hierarchy,
* e.g. view all details for one specific role AND only for a certain course
============================================================================== 
*/

/*
============================================================================== 
		INIT SECTION
============================================================================== 
*/ 

$langFile = "admin"; 
include("../inc/claro_init_global.inc.php"); 
$this_section=SECTION_PLATFORM_ADMIN;

api_protect_admin_script();

/*
-----------------------------------------------------------
	Constants
-----------------------------------------------------------
*/ 

define ("EDIT", "edit");

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/ 
$tool_name = get_lang("RolesRightsOverview"); // title of the page (should come from the language file) 
$interbredcrump[]=array("url" => "index.php","name" => get_lang("PlatformAdmin"));
Display::display_header($tool_name);

/*
============================================================================== 
		FUNCTIONS
============================================================================== 
*/ 

/**
* Shows a table overview for one specific role.
* For this we first create a 2D array with data, then display it
* One axis becomes the rights axis, the other the location axis.
* the elements inside the table are the values (true or false)
* for every right / location combination.
*/
function display_overview_focus_role($role_id)
{
	$focus_on_role_data = RolesRights::get_focus_on_role_data($role_id);
	$two_axis_table_data = $focus_on_role_data['raw_data'];
	$two_axis_table_data_visual = $focus_on_role_data['visual_data'];
	$x_limit = $focus_on_role_data['x_limit'];
	$y_limit = $focus_on_role_data['y_limit'];
	
	echo "<p><strong>Overview for role " . RolesRights::get_visual_role_name($role_id) . "</strong></p>";
	echo '<table class="data_table" width="100%">';
	for ($y = 0; $y <= $y_limit; $y++)
	{
		//if ($y > 0 && $two_axis_table_data_visual[0][$y] == '') continue;
		echo '<tr>';
		for ($x = 0; $x <= $x_limit; $x++)
		{
			$value = $two_axis_table_data_visual[$x][$y];
			if ($value == "true")
				$value_output = '<a class="role_right_true" href="?action='.EDIT.'&role_id='. $role_id . '&location_id=' . $two_axis_table_data[0][$y] . '&right_id='. $two_axis_table_data[$x][0] . '&original_value=' . $value . '&focus_role=true&role_list='.$role_id.'"><font color=\"green\">'.'<image src="../img/setting_true.gif">'.'</font></a>';
			else if ($value == "false")
				$value_output = '<a class="role_right_false" href="?action='.EDIT.'&role_id='. $role_id . '&location_id=' . $two_axis_table_data[0][$y] . '&right_id='. $two_axis_table_data[$x][0] . '&original_value=' . $value . '&focus_role=true&role_list='.$role_id.'"><font color=\"green\">'.'<image src="../img/setting_false.gif">'.'</font></a>';
			else $value_output = $value;
			
			echo '<td>' . $value_output . '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}

/**
* Shows a table overview for one specific right.
* For this we first create a 2D array, then display it
* One axis becomes the roles axis, the other the location axis.
* the elements inside the table are the values (true or false)
* for every role / location combination.
*/
function display_overview_focus_right($right_id)
{
	$focus_on_right_data = RolesRights::get_focus_on_right_data($right_id);
	$two_axis_table_data = $focus_on_right_data['raw_data'];
	$two_axis_table_data_visual = $focus_on_right_data['visual_data'];
	$x_limit = $focus_on_right_data['x_limit'];
	$y_limit = $focus_on_right_data['y_limit'];
	
	echo '<strong>Overview for right ' . RolesRights::get_visual_right_name($right_id) . '</strong><br/>';
	echo '<table class="data_table" width="100%">';
	for ($y = 0; $y <= $y_limit; $y++)
	{
		if ($y > 0 && $two_axis_table_data_visual[0][$y] == '') continue;
		echo '<tr>';
		for ($x = 0; $x <= $x_limit; $x++)
		{
			$value = $two_axis_table_data_visual[$x][$y];
			//if ($value == "true") $value = "<font color=\"green\">$value</font>";
			//else if ($value == "false") $value = "<font color=\"red\">$value</font>";
			//echo '<td>' . $value . '</td>';
			
			if ($value == "true")
				$value_output = '<a class="role_right_true" href="?action='.EDIT.'&role_id='. $two_axis_table_data[$x][0] . '&location_id=' . $two_axis_table_data[0][$y] . '&right_id='. $right_id . '&original_value=' . $value . '&focus_right=true&right_list='.$right_id.'"><font color=\"green\">'.'<image src="../img/setting_true.gif">'.'</font></a>';
			else if ($value == "false")
				$value_output = '<a class="role_right_false" href="?action='.EDIT.'&role_id='. $two_axis_table_data[$x][0] . '&location_id=' . $two_axis_table_data[0][$y] . '&right_id='. $right_id . '&original_value=' . $value . '&focus_right=true&right_list='.$right_id.'"><font color=\"green\">'.'<image src="../img/setting_false.gif">'.'</font></a>';
			else $value_output = $value;
			echo '<td>' . $value_output . '</td>';
			
		}
		echo '</tr>';
	}
	echo '</table>';
}

/**
* Shows a table overview for one specific location.
* For this we first create a 2D array, then display it
* One axis is the role axis, the other the rights axis.
* The elements inside the table are the values (true or false)
* for every role / right combination.
*/
function display_overview_focus_location($selected_location_id)
{
	$focus_on_location_data = RolesRights::get_focus_on_location_data($selected_location_id);
	$two_axis_table_data = $focus_on_location_data['raw_data'];
	$two_axis_table_data_visual = $focus_on_location_data['visual_data'];
	$x_limit = $focus_on_location_data['x_limit'];
	$y_limit = $focus_on_location_data['y_limit'];
	
	$selected_location = RolesRights::get_location_from_location_id($selected_location_id);
	echo '<strong>Overview for location ' . RolesRights::create_nice_visual_location($selected_location) . '</strong><br/>';
	
	echo '<table class="data_table" width="100%">';
	for ($y = 0; $y <= $y_limit; $y++)
	{
		if ($y > 0 && $two_axis_table_data_visual[0][$y] == '') continue;
		echo '<tr>';
		for ($x = 0; $x <= $x_limit; $x++)
		{
			$value = $two_axis_table_data_visual[$x][$y];
			//if ($value == "true") $value = "<font color=\"green\">$value</font>";
			//else if ($value == "false") $value = "<font color=\"red\">$value</font>";
			//echo '<td>' . $value . '</td>';
			if ($value == "true")
				$value_output = '<a class="role_right_true" href="?action='.EDIT.'&role_id='. $two_axis_table_data[0][$y] . '&location_id=' . $selected_location_id . '&right_id='. $two_axis_table_data[$x][0] . '&original_value=' . $value . '&focus_location=true&location_list='.$selected_location_id.'"><font color=\"green\">'.'<image src="../img/setting_true.gif">'.'</font></a>';
			else if ($value == "false")
				$value_output = '<a class="role_right_false" href="?action='.EDIT.'&role_id='. $two_axis_table_data[0][$y] . '&location_id=' . $selected_location_id . '&right_id='. $two_axis_table_data[$x][0] . '&original_value=' . $value . '&focus_location=true&location_list='.$selected_location_id.'"><font color=\"green\">'.'<image src="../img/setting_false.gif">'.'</font></a>';
			else $value_output = $value;
			echo '<td>' . $value_output . '</td>';
			
		}
		echo '</tr>';
	}
	echo '</table>';
}

/*
============================================================================== 
		MAIN SECTION
============================================================================== 
*/ 

$action = $_REQUEST['action'];

api_display_tool_title($tool_name);

//for 1.7, global roles are not used - hide them from the interface
//RolesRights::display_role_choice();
RolesRights::display_local_role_choice();
RolesRights::display_right_choice();
RolesRights::display_location_choice();

if (isset($action) && $action == EDIT)
{
	$role_id = $_REQUEST['role_id'];
	$location_id = $_REQUEST['location_id'];
	$right_id = $_REQUEST['right_id'];
	$original_value = $_REQUEST['original_value'];
	$new_value = $original_value == "true" ? false : true;
	RolesRights::set_value($role_id, $right_id, $location_id, $new_value);
	Display::display_normal_message(get_lang("RightValueModified"));
}

if (isset($_REQUEST['focus_role']) && $_REQUEST['focus_role'])
{
	$selected_role = $_REQUEST['role_list'];
	display_overview_focus_role($selected_role);
}
if (isset($_REQUEST['focus_right']) && $_REQUEST['focus_right'])
{
	$selected_right = $_REQUEST['right_list'];
	display_overview_focus_right($selected_right);
}
else if (isset($_REQUEST['focus_location']) && $_REQUEST['focus_location'])
{
	$selected_location = $_REQUEST['location_list'];
	display_overview_focus_location($selected_location);
}

/*
==============================================================================
		FOOTER 
==============================================================================
*/ 
Display::display_footer();
?>