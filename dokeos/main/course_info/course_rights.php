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
* This page displays an overview of the role / rights settings for the current
* course and also allows to change the values.
*
* This page shows only local roles, and only locations inside this course
* (currently the course itself and the course tools).
*
* @todo build in more filter options to create less complex looking pages
* @todo finetune user interface
* @todo finetune code for setting modified or specific course visibility
==============================================================================
*/
/*
==============================================================================
	   INIT SECTION
==============================================================================
*/
api_use_lang_files('course_info');
include ('../inc/global.inc.php');
$this_section = SECTION_COURSES;

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/

/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
define('MODULE_HELP_NAME', 'CourseRolesRights');
$course_code = $_course['sysCode'];

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
function display_overview_focus_role($role_id,$is_allowed)
{
	$role_description = RolesRights::get_role_description($role_id);
	
	echo '<p><strong>Overview for role ' . RolesRights::get_visual_role_name($role_id) . '</strong><br/>' . $role_description . '</p>';
	
	$course_code = api_get_course_id();
	$focus_on_role_data = RolesRights::get_focus_on_role_data($role_id);
	$two_axis_table_data = $focus_on_role_data['raw_data'];
	$two_axis_table_data_visual = $focus_on_role_data['visual_data'];
	$x_limit = $focus_on_role_data['x_limit'];
	$y_limit = $focus_on_role_data['y_limit'];
	
	//echo data
	$table_data = array();
	unset($two_axis_table_data_visual[0][0]);
	for ($y = 1; $y <= $y_limit; $y++)
	{
		if ( $y > 0)
		{
			$location = $two_axis_table_data_visual[0][$y];
			//echo "<td>location = $location</td>";
			if (! stristr($location, $course_code)) continue;
		}
		$table_row = array();
		for ($x = 0; $x < $x_limit; $x++)
		{
			$value = $two_axis_table_data_visual[$x][$y];
			if ($value == 'true')
			{
				$value_output = '<img src="../img/setting_true.gif" alt="'.get_lang('Yes').'" />';
				if( $is_allowed[EDIT_RIGHT])
				{
					$value_output = '<a class="role_right_true" href="?action='.EDIT.'&amp;role_id='. $role_id . '&amp;location_id=' . $two_axis_table_data[0][$y] . '&amp;right_id='. $two_axis_table_data[$x][0] . '&amp;original_value=' . $value . '&amp;focus_role=true&amp;role_list='.$role_id.'">'.$value_output.'</a>';
				}
			}
			else if ($value == 'false')
			{
				$value_output = '<img src="../img/setting_false.gif" alt="'.get_lang('No').'" />';
				if( $is_allowed[EDIT_RIGHT])
				{
					$value_output = '<a class="role_right_false" href="?action='.EDIT.'&amp;role_id='. $role_id . '&amp;location_id=' . $two_axis_table_data[0][$y] . '&amp;right_id='. $two_axis_table_data[$x][0] . '&amp;original_value=' . $value . '&amp;focus_role=true&amp;role_list='.$role_id.'">'.$value_output.'</a>';
				}
			}
			else $value_output = $value;
			$table_row[] = $value_output;
		}
		$table_data[] = $table_row;
	}
	$table = new SortableTableFromArray($table_data,0);
	$parameters['focus_role'] = $_REQUEST['focus_role'];
	$parameters['role_list'] = $_REQUEST['role_list'];
	$table->set_additional_parameters($parameters);
	$table->set_header(0,get_lang('Location'));
	foreach($two_axis_table_data_visual as $index => $item)
	{
		if($index != 0)
		{
			$table->set_header($index,$item[0],false);
		}	
	}
	$table->display();
}

/**
* Shows a table overview for one specific right.
* For this we first create a 2D array, then display it
* One axis becomes the roles axis, the other the location axis.
* the elements inside the table are the values (true or false)
* for every role / location combination.
*/
function display_overview_focus_right($right_id,$is_allowed)
{
	$focus_on_right_data = RolesRights::get_focus_on_right_data($right_id);
	$two_axis_table_data = $focus_on_right_data['raw_data'];
	$two_axis_table_data_visual = $focus_on_right_data['visual_data'];
	$x_limit = $focus_on_right_data['x_limit'];
	$y_limit = $focus_on_right_data['y_limit'];
	
	$course_code = api_get_course_id();
	$relation_table = Database::get_main_table(MAIN_ROLE_RIGHT_LOCATION_TABLE);
	$sql_query = "SELECT * FROM $relation_table WHERE right_id='$right_id' ORDER BY location_id";
	$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
	while ($result = mysql_fetch_array($sql_result, MYSQL_NUM))
	{
		if ($result[3] == '1') $result[3] = 'true';
		else $result[3] = 'false';
		$overview[] = $result;
	}
	
	echo '<p><strong>Overview for right ' . RolesRights::get_visual_right_name($right_id) . '</strong></p>';
	$table_data = array();
	unset($two_axis_table_data_visual[0][0]);
	for ($y = 1; $y <= $y_limit; $y++)
	{
		if ( $y > 0)
		{
			$location = $two_axis_table_data_visual[0][$y];
			//echo "<td>location = $location</td>";
			if (! stristr($location, $course_code)) continue;
		}
		$table_row = array();
		for ($x = 0; $x < $x_limit; $x++)
		{
			$value = $two_axis_table_data_visual[$x][$y];
			if ($value == 'true')
			{
				$value_output = '<img src="../img/setting_true.gif" alt="'.get_lang('Yes').'" />';
				if( $is_allowed[EDIT_RIGHT])
				{
					$value_output = '<a class="role_right_true" href="?action='.EDIT.'&amp;role_id='. $two_axis_table_data[$x][0] . '&amp;location_id=' . $two_axis_table_data[0][$y] . '&amp;right_id='. $right_id . '&amp;original_value=' . $value . '&amp;focus_right=true&amp;right_list='.$right_id.'">'.$value_output.'</a>';
				}
			}
			else if ($value == 'false')
			{
				$value_output = '<img src="../img/setting_false.gif" alt="'.get_lang('No').'" />';
				if( $is_allowed[EDIT_RIGHT])
				{
					$value_output = '<a class="role_right_false" href="?action='.EDIT.'&amp;role_id='. $two_axis_table_data[$x][0] . '&amp;location_id=' . $two_axis_table_data[0][$y] . '&amp;right_id='. $right_id . '&amp;original_value=' . $value . '&amp;focus_right=true&amp;right_list='.$right_id.'">'.$value_output.'</a>';
				}
			}
			else $value_output = $value;
			$table_row[] = $value_output;
			
		}
		$table_data[] = $table_row;
	}
	$table = new SortableTableFromArray($table_data,0);
	$parameters['focus_right'] = $_REQUEST['focus_right'];
	$parameters['right_list'] = $_REQUEST['right_list'];
	$table->set_additional_parameters($parameters);
	$table->set_header(0,get_lang('Location'));
	foreach($two_axis_table_data_visual as $index => $item)
	{
		if($index != 0)
		{
			$table->set_header($index,$item[0],false);
		}	
	}
	$table->display();
}

/**
* Shows a table overview for one specific location.
* For this we first create a 2D array, then display it
* One axis is the role axis, the other the rights axis.
* The elements inside the table are the values (true or false)
* for every role / right combination.
*/
function display_overview_focus_location($selected_location_id,$is_allowed)
{
	$focus_on_location_data = RolesRights::get_focus_on_location_data($selected_location_id);
	$two_axis_table_data = $focus_on_location_data['raw_data'];
	$two_axis_table_data_visual = $focus_on_location_data['visual_data'];
	$x_limit = $focus_on_location_data['x_limit'];
	$y_limit = $focus_on_location_data['y_limit'];
	
	$selected_location = RolesRights::get_location_from_location_id($selected_location_id);
	echo '<p><strong>Overview for location ' . RolesRights::create_nice_visual_location($selected_location) . '</strong></p>';
	$table_data = array();
	unset($two_axis_table_data_visual[0][0]);
	for ($y = 1; $y <= $y_limit; $y++)
	{
		if ($y > 0 && $two_axis_table_data_visual[0][$y] == '') continue;
		$table_row = array();
		for ($x = 0; $x < $x_limit; $x++)
		{
			$value = $two_axis_table_data_visual[$x][$y];
			if ($value == 'true')
			{
				$value_output = '<img src="../img/setting_true.gif" alt="'.get_lang('Yes').'" />';
				if( $is_allowed[EDIT_RIGHT])
				{
					$value_output = '<a class="role_right_true" href="?action='.EDIT.'&amp;role_id='. $two_axis_table_data[0][$y] . '&amp;location_id=' . $selected_location_id . '&amp;right_id='. $two_axis_table_data[$x][0] . '&amp;original_value=' . $value . '&amp;focus_location=true&amp;location_list='.$selected_location_id.'">'.$value_output.'</a>';
				}
			}
			elseif ($value == 'false')
			{
				$value_output = '<img src="../img/setting_false.gif" alt="'.get_lang('No').'" />';
				if( $is_allowed[EDIT_RIGHT])
				{
					$value_output = '<a class="role_right_false" href="?action='.EDIT.'&amp;role_id='. $two_axis_table_data[0][$y] . '&amp;location_id=' . $selected_location_id . '&amp;right_id='. $two_axis_table_data[$x][0] . '&amp;original_value=' . $value . '&amp;focus_location=true&amp;location_list='.$selected_location_id.'">'.$value_output.'</a>';
				}
			}
			else
			{
				$value_output = $value;
			}
			$table_row[] = '<span style="display:none">'.$y.'</span>'.$value_output;	
		}
		$table_data[] = $table_row;
	}
	$table = new SortableTableFromArray($table_data,0);
	$parameters['focus_location'] = $_REQUEST['focus_location'];
	$parameters['location_list'] = $_REQUEST['location_list'];
	$table->set_additional_parameters($parameters);
	$table->set_header(0,get_lang('UserRole'),false);
	foreach($two_axis_table_data_visual as $index => $item)
	{
		if($index != 0)
		{
			$table->set_header($index,$item[0],false);
		}	
	}
	$table->display();
}


/*
==============================================================================
		MAIN CODE
==============================================================================
*/
$nameTools = get_lang("OverviewCourseRights");
Display :: display_header($nameTools, MODULE_HELP_NAME);
api_display_tool_title($nameTools);

//new roles and rights system for 1.7
$user_id = api_get_user_id();
$course_id = api_get_course_id();
$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
$location_id = RolesRights::get_course_tool_location_id($course_id, TOOL_COURSE_RIGHTS_OVERVIEW);
$is_allowed = RolesRights::is_allowed_which_rights($role_id, $location_id);

//block users without view right
RolesRights::protect_location($role_id, $location_id);

$action = $_REQUEST['action'];
if ($is_allowed[EDIT_RIGHT] && isset($action) && $action == EDIT)
{
	$this_user_id = api_get_user_id();
	$this_course_id = api_get_course_id();
	$this_course_location_id = RolesRights::get_course_location_id($this_course_id);
	$this_user_role_id = RolesRights::get_local_user_role_id($this_user_id, $this_user_role_id);
	
	$role_id = $_REQUEST['role_id'];
	$location_id = $_REQUEST['location_id'];
	$right_id = $_REQUEST['right_id'];
	$original_value = $_REQUEST['original_value'];
	$new_value = $original_value == 'true' ? false : true;
	
	if ($this_user_role_id == $role_id)
	{
		Display::display_error_message(get_lang('NotAllowedToModifyOwnRole'));
	}
	else
	{
		RolesRights::set_value($role_id, $right_id, $location_id, $new_value);
		if ($right_id == VIEW_RIGHT && $location_id == $this_course_location_id)
		{
			//view rights adjusted, set course visibility indication to 'modified'
			$course_table = Database::get_main_table(MAIN_COURSE_TABLE);
			$adjust_visibility_sql = "UPDATE $course_table 
					SET visibility  = '".COURSE_VISIBILITY_MODIFIED."' 
					WHERE code = '".$this_course_id."'";
			api_sql_query($adjust_visibility_sql, __FILE__, __LINE__);
		}
		Display::display_normal_message(get_lang('RightValueModified'));
	}
}

RolesRights::display_local_role_choice();
RolesRights::display_right_choice();
RolesRights::display_location_choice($course_code);

if (isset($_REQUEST['focus_role']) && $_REQUEST['focus_role'])
{
	$selected_role = $_REQUEST['role_list'];
	display_overview_focus_role($selected_role,$is_allowed);
}
elseif (isset($_REQUEST['focus_right']) && $_REQUEST['focus_right'])
{
	$selected_right = $_REQUEST['right_list'];
	display_overview_focus_right($selected_right,$is_allowed);
}
elseif (isset($_REQUEST['focus_location']) && $_REQUEST['focus_location'])
{
	$selected_location = $_REQUEST['location_list'];
	display_overview_focus_location($selected_location,$is_allowed);
}
else
{
	$default_location_id = RolesRights::get_course_location_id($course_code);
	display_overview_focus_location($default_location_id,$is_allowed);
}

/*
==============================================================================
		FOOTER
==============================================================================
*/
Display :: display_footer();
?>