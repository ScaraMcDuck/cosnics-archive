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
*	This is the user roles and rights library.
*	Include/require it in your code to use its functionality.
*
*	@package dokeos.library
==============================================================================
*/

define('LOCATION_PATH_SEPARATOR', '|');

define('VIEW_RIGHT', 1);
define('EDIT_RIGHT', 2);
define('ADD_RIGHT', 3);
define('DELETE_RIGHT', 4);

define('DEFAULT_COURSE', 'DEFAULT');

//deprecated, use ANONYMOUS_GUEST_COURSE_VISITOR instead
define('ANONYMOUS_GUEST_COURSE_VISITOR_ROLE', 5);
define('ANONYMOUS_GUEST_COURSE_VISITOR', 5);
define('REGISTERED_GUEST_COURSE_VISITOR', 6);
define('NORMAL_COURSE_MEMBER', 7);
define('TEACHING_ASSISTANT', 8);
define('COURSE_ADMIN', 9);

class RolesRights
{
	
	/**
	* Returns a list of roles retrieved from the database.
	* 
	* @return array of roles 
	* @param $result_type MYSQL_ASSOC or MYSQL_NUM
	*/
	function get_role_list($result_type=MYSQL_ASSOC)
	{
		$role_table = Database::get_main_table(MAIN_ROLE_TABLE);
		$sql_query = "SELECT * FROM $role_table ORDER BY id";
		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		
		while ($result = mysql_fetch_array($sql_result, $result_type))
		{
			$role_list[] = $result;
		}
		return $role_list;
	}
	
	/**
	* Returns a list of local roles retrieved from the database.
	* 
	* @return array of local roles 
	* @param $result_type MYSQL_ASSOC or MYSQL_NUM
	*/
	function get_local_role_list($result_type=MYSQL_ASSOC)
	{
		$role_table = Database::get_main_table(MAIN_ROLE_TABLE);
		$sql_query = "SELECT * FROM $role_table WHERE type='local' ORDER BY id";
		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		
		while ($result = mysql_fetch_array($sql_result, $result_type))
		{
			$role_list[] = $result;
		}
		return $role_list;
	}
	
	function get_right_list()
	{
		$right_table = Database::get_main_table(MAIN_RIGHT_TABLE);
		$sql_query = "SELECT * FROM $right_table ORDER BY id";
		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		
		while ($result = mysql_fetch_array($sql_result, MYSQL_ASSOC))
		{
			$right_list[] = $result;
		}
		return $right_list;
	}
	
	function get_location_list($course_filter = '')
	{
		$location_table = Database::get_main_table(MAIN_LOCATION_TABLE);
		if ($course_filter == '')
		{
			$sql_query = "SELECT id, location FROM $location_table ORDER BY location";
		}
		else
		{
			$course_location = RolesRights::get_short_course_location_path($course_filter);
			$sql_query = "SELECT id, location FROM $location_table WHERE ( location LIKE '%$course_location|%' ) OR ( location LIKE '%$course_location' ) ORDER BY location";
		}
		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		
		while ($result = mysql_fetch_array($sql_result, MYSQL_ASSOC))
		{
			$location_list[] = $result;
		}
		return $location_list;
	}
	
	function display_role_choice()
	{
		$roles = RolesRights::get_role_list();
		echo "<form id=\"role_form\" method=\"get\" action=\"{$_SERVER['PHP_SELF']}\">\n";
		echo "<p>" . get_lang("SeeAllRightsAllLocationsForSpecificRole") . "&nbsp;\n";
		echo "<select name=\"role_list\">\n";
		foreach ($roles as $current_role)
		{
			$id = $current_role['id'];
			$name = RolesRights::get_visual_role_name_from_name($current_role['name']);
			echo "\t<option value=\"$id\">";
			echo $name;
			echo "</option>\n";
		}
		echo "</select>\n";
		echo '<input type="submit" name="focus_role" value="OK" />';
		echo "\n</p>\n</form>\n";
	}
	
	function display_local_role_choice()
	{
		$roles = RolesRights::get_local_role_list();
		echo "<form id=\"role_form\" method=\"get\" action=\"{$_SERVER['PHP_SELF']}\">\n";
		echo "<p>" . get_lang("SeeAllRightsAllLocationsForSpecificRole") . "&nbsp;\n";
		echo "<select name=\"role_list\">\n";
		foreach ($roles as $current_role)
		{
			$id = $current_role['id'];
			$name = RolesRights::get_visual_role_name_from_name($current_role['name']);
			echo "\t<option value=\"$id\">";
			echo $name;
			echo "</option>\n";
		}
		echo "</select>\n";
		echo '<input type="submit" name="focus_role" value="OK" />';
		echo "\n</p>\n</form>\n";
	}
	
	function display_right_choice()
	{
		$rights = RolesRights::get_right_list();
		echo "<form id=\"right_form\" method=\"get\" action=\"{$_SERVER['PHP_SELF']}\">\n";
		echo "<p>" . get_lang("SeeAllRolesAllLocationsForSpecificRight") . "&nbsp;\n";
		echo "<select name=\"right_list\">\n";
		
		foreach ($rights as $current_right)
		{
			$id = $current_right['id'];
			$name = $current_right['name'];
			echo "\t<option value=\"$id\">";
			echo get_lang($name);
			echo "</option>\n";
		}
		echo "</select>\n";
		echo '<input type="submit" name="focus_right" value="OK" />';
		echo "\n</p>\n</form>\n";
	}
	
	function display_location_choice($filter='')
	{
		$locations = RolesRights::get_location_list();
		echo "<form id=\"location_form\" method=\"get\" action=\"{$_SERVER['PHP_SELF']}\">\n";
		echo "<p>" . get_lang("SeeAllRightsAllRolesForSpecificLocation") . "&nbsp;\n";
		echo "<select name=\"location_list\">\n";
		
		foreach ($locations as $current_location)
		{
			$id = $current_location['id'];
			if ($filter == '' || stristr($current_location['location'], $filter) )
			{
				$name = RolesRights::create_nice_visual_location($current_location['location']);
				echo "\t<option value=\"$id\">";
				echo $name;
				echo "</option>\n";
			}
		}
		echo "</select>\n";
		echo '<input type="submit" name="focus_location" value="OK" />';
		echo "\n</p>\n</form>\n";
	}
	
	/**
	* @param $role_id, the id of the role
	* @return the name of the role
	*/
	function get_role_name($role_id)
	{
		$role_table = Database::get_main_table(MAIN_ROLE_TABLE);
		$sql_query = "SELECT * FROM $role_table WHERE id='$role_id'";
		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		$result = mysql_fetch_array($sql_result, MYSQL_ASSOC);
		$name = $result['name'];
		return $name;
	}
	
	/**
	* @param $role_id, the id of the role
	* @return the translated name of the role
	*/
	function get_visual_role_name($role_id)
	{
		return get_lang(RolesRights::get_role_name($role_id));
	}
	
	function get_visual_role_name_from_name($role_name)
	{
		$visual_name = get_lang($role_name);
		if (stristr($visual_name, "[=")) return $role_name;
		else return $visual_name;
	}
	
	/**
	* @param $role_id, the id of the role
	* @return the translated description of the role
	*/
	function get_role_description($role_id)
	{
		$role_table = Database::get_main_table(MAIN_ROLE_TABLE);
		$sql_query = "SELECT * FROM $role_table WHERE id='$role_id'";
		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		$result = mysql_fetch_array($sql_result);
		$description = get_lang($result['description']);
		return $description;
	}
	
	/**
	* @param $right_id, the id of the right
	* @return the translated name of the right
	*/
	function get_visual_right_name($right_id)
	{
		return get_lang(RolesRights::get_right_name($right_id));
	}
	
	/**
	* @param $right_id, the id of the right
	* @return the name of the right
	*/
	function get_right_name($right_id)
	{
		$right_table = Database::get_main_table(MAIN_RIGHT_TABLE);
		$sql_query = "SELECT * FROM $right_table WHERE id='$right_id'";
		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		$result = mysql_fetch_array($sql_result, MYSQL_ASSOC);
		$name = $result['name'];
		return $name;
	}
	
	function get_location_from_location_id($location_id)
	{
		$location_table = Database::get_main_table(MAIN_LOCATION_TABLE);
		$sql_query = "SELECT * FROM $location_table WHERE id='$location_id'";
		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		$result = mysql_fetch_array($sql_result, MYSQL_ASSOC);
		$location = $result['location'];
		return $location;
	}
	
	/**
	* This function creates a presentation that looks better for users
	* based on the real location string(e.g. platform|courses|course,GGG|tool,document)
	* 
	* @return string visual_location
	*/
	function create_nice_visual_location($location)
	{
		$results = array();
		$match_found = preg_match("/platform\|courses\|course,(.*)\|(.*)/i", $location, $results);
		if ($match_found)
		{
			$course_code = $results[1];
			$tool_location = $results[2];
			$tool_result = array();

			preg_match("/tool,(.*)/i", $tool_location, $tool_result);
			$visual_tool_location = get_lang($tool_result[1]);
			
			$result_string = "Course $course_code, $visual_tool_location";
			return $result_string;
		}
		
		$match_found = preg_match("/platform\|courses\|course,(.*)/i", $location, $results);
		if ($match_found)
		{
			$course_code = $results[1];
			$tool_location = $results[2];
			$result_string = "Course $course_code";
			return $result_string;
		}
		
		return false;
	}
	
	/**
	* Creates a new local role.
	*
	* @param $role_name the name of the new role
	* @param $role_description the description of the new role
	* @param $user_id the creator of the new role
	* @param $based_on_role the id of the role whose default rights are used to provide the initial default rights of the new role - these can be modified later
	* @todo create default set of roles-rights settings for the new role based on the settings from $based_on_role
	* @todo return true or false
	*/
	function create_local_role($role_name, $role_description, $user_id, $based_on_role)
	{
		//echo "Creating role $role_name by user $user_id based on role $based_on_role.";
		$role_table = Database::get_main_table(MAIN_ROLE_TABLE);
		$sql = "INSERT INTO $role_table VALUES ('','$role_name', 'local', $user_id, '$role_description')";
		api_sql_query($sql, __FILE__, __LINE__);
	}
	
	/**
	* Creates a new global role.
	*
	* @param $role_name the name of the new role
	* @param $role_description the description of the new role
	* @param $user_id the creator of the new role
	* @param $based_on_role the id of the role whose default rights are used to provide the initial default rights of the new role - these can be modified later
	* @todo return true or false
	*/
	function create_global_role($role_name, $role_description, $user_id)
	{
		//echo "Creating role $role_name by user $user_id based on role $based_on_role.";
		$role_table = Database::get_main_table(MAIN_ROLE_TABLE);
		$sql = "INSERT INTO $role_table VALUES ('','$role_name', 'global', $user_id, '$role_description')";
		api_sql_query($sql, __FILE__, __LINE__);
	}
	
	/**
	* Returns rights/locations data for one specific role.
	* @return an array with elements:
	* 'raw_data' - the raw data in 2D array format
	* 'visual_data' - the raw data "made nice" for display
	* 'x_limit' and 'y_limit' - the dimensions of the arrays
	*/
	function get_focus_on_role_data($role_id, $course_filter='')
	{
		/*
			New approach: all rights and locations will be displayed.
			If there is no value set, we assume it is false and allow to edit the value to true.
		*/
		$right_list = RolesRights::get_right_list();
		$location_list = RolesRights::get_location_list($course_filter);
		$x_limit = count($right_list) + 1;
		$y_limit = count($location_list) + 1;
		
		//add column titles listing all rights
		for ($x = 0; $x < count($right_list); $x++)
		{
			$right_id = $right_list[$x]['id'];
			$two_axis_table_data[$x+1][0] = $right_id;
			$two_axis_table_data_visual[$x+1][0] = '<strong>' . RolesRights::get_visual_right_name($right_id) . '</strong>';
		}
		
		//add row titles listing all locations
		for ($y = 0; $y < count($location_list); $y++)
		{
			$location_id = $location_list[$y]['id'];
			$location = RolesRights::get_location_from_location_id($location_id);
			$visual_location = RolesRights::create_nice_visual_location($location);
			$two_axis_table_data[0][$y+1] = $location_id;
			$two_axis_table_data_visual[0][$y+1] = $visual_location;
		}
		
		//fill in rest of table with true/false icons or text
		for ($x = 1; $x < $x_limit; $x++)
		{
			for ($y = 1; $y < $y_limit; $y++)
			{
				$right_id = $right_list[$x-1]['id'];
				$location_id = $location_list[$y-1]['id'];
				$value = RolesRights::is_allowed($role_id, $right_id, $location_id);
				$two_axis_table_data[$x][$y] = $value;
				if ($value == '1') $value = 'true';
				else $value = 'false';
				$two_axis_table_data_visual[$x][$y] = $value;
			}
		}
		
		$result['raw_data'] = $two_axis_table_data;
		$result['visual_data'] = $two_axis_table_data_visual;
		$result['x_limit'] = $x_limit;
		$result['y_limit'] = $y_limit;
		return $result;
	}
	
	/**
	* Returns roles/locations data for one specific right.
	* @return an array with elements:
	* 'raw_data' - the raw data in 2D array format
	* 'visual_data' - the raw data "made nice" for display
	* 'x_limit' and 'y_limit' - the dimensions of the arrays
	*/
	function get_focus_on_right_data($right_id)
	{
		$role_list = RolesRights::get_local_role_list();
		$location_list = RolesRights::get_location_list();
		$x_limit = count($role_list) + 1;
		$y_limit = count($location_list) + 1;
		
		//add column titles listing all roles
		for ($x = 0; $x < count($role_list); $x++)
		{
			$role_id = $role_list[$x]['id'];
			$two_axis_table_data[$x+1][0] = $role_id;
			$two_axis_table_data_visual[$x+1][0] = '<strong>' . RolesRights::get_visual_role_name($role_id) . '</strong>';
		}
		
		//add row titles listing all locations
		for ($y = 0; $y < count($location_list); $y++)
		{
			$location_id = $location_list[$y]['id'];
			$location = RolesRights::get_location_from_location_id($location_id);
			$visual_location = RolesRights::create_nice_visual_location($location);
			$two_axis_table_data[0][$y+1] = $location_id;
			$two_axis_table_data_visual[0][$y+1] = $visual_location;
		}
		
		//fill in rest of table with true/false icons or text
		for ($x = 1; $x < $x_limit; $x++)
		{
			for ($y = 1; $y < $y_limit; $y++)
			{
				$role_id = $role_list[$x-1]['id'];
				$location_id = $location_list[$y-1]['id'];
				$value = RolesRights::is_allowed($role_id, $right_id, $location_id);
				$two_axis_table_data[$x][$y] = $value;
				if ($value == '1') $value = 'true';
				else $value = 'false';
				$two_axis_table_data_visual[$x][$y] = $value;
			}
		}
		
		$result['raw_data'] = $two_axis_table_data;
		$result['visual_data'] = $two_axis_table_data_visual;
		$result['x_limit'] = $x_limit;
		$result['y_limit'] = $y_limit;
		return $result;
	}
	
	/**
	* Returns roles/rights/ data for one specific role.
	* @return an array with elements:
	* 'raw_data' - the raw data in 2D array format
	* 'visual_data' - the raw data "made nice" for display
	* 'x_limit' and 'y_limit' - the dimensions of the arrays
	*/
	function get_focus_on_location_data($location_id)
	{
		$right_list = RolesRights::get_right_list();
		$role_list = RolesRights::get_local_role_list();
		$x_limit = count($right_list) + 1;
		$y_limit = count($role_list) + 1;
		
		//add column titles listing all rights
		for ($x = 0; $x < count($right_list); $x++)
		{
			$right_id = $right_list[$x]['id'];
			$two_axis_table_data[$x+1][0] = $right_id;
			$two_axis_table_data_visual[$x+1][0] = '<strong>' . RolesRights::get_visual_right_name($right_id) . '</strong>';
		}
		
		//add row titles listing all roles
		for ($y = 0; $y < count($role_list); $y++)
		{
			$role_id = $role_list[$y]['id'];
			$visual_role = RolesRights::get_visual_role_name($role_id);
			$two_axis_table_data[0][$y+1] = $role_id;
			$two_axis_table_data_visual[0][$y+1] = $visual_role;
		}
		
		//fill in rest of table with true/false icons or text
		for ($x = 1; $x < $x_limit; $x++)
		{
			for ($y = 1; $y < $y_limit; $y++)
			{
				$right_id = $right_list[$x-1]['id'];
				$role_id = $role_list[$y-1]['id'];
				$value = RolesRights::is_allowed($role_id, $right_id, $location_id);
				$two_axis_table_data[$x][$y] = $value;
				if ($value == '1') $value = 'true';
				else $value = 'false';
				$two_axis_table_data_visual[$x][$y] = $value;
			}
		}
		
		$result['raw_data'] = $two_axis_table_data;
		$result['visual_data'] = $two_axis_table_data_visual;
		$result['x_limit'] = $x_limit;
		$result['y_limit'] = $y_limit;
		return $result;
	}
	
	/**
	* @return the local role id of user $user_id in course $course_id
	* Currently this function takes only one local user role into account.
	*/
	function get_local_user_role_id($user_id, $course_id)
	{
		$user_role_table = Database::get_main_table(MAIN_USER_ROLE_TABLE);
		$location_table = Database::get_main_table(MAIN_LOCATION_TABLE);
		
		$short_location = RolesRights::get_short_course_location_path($course_id);
		$location_id = RolesRights::get_course_location_id_from_short_location($short_location);
		
		$find_role_id_sql = "SELECT role_id FROM $user_role_table WHERE user_id='$user_id' AND location_id='$location_id'";
		$sql_result = api_sql_query($find_role_id_sql, __FILE__, __LINE__);
		$result = mysql_fetch_array($sql_result);
		$role_id = $result['role_id'];
		
		if (! isset($role_id) ) 
		{
			if (api_is_platform_admin()) $role_id = COURSE_ADMIN;
			else $role_id = ANONYMOUS_GUEST_COURSE_VISITOR;
		}
		
		return $role_id;
	}
	
	/**
	* @return the local role id of user $user_id in location $location_id
	* Currently this function takes only one local user role into account.
	*/
	function get_local_user_role_id_from_location_id($user_id, $location_id)
	{
		$user_role_table = Database::get_main_table(MAIN_USER_ROLE_TABLE);
		$find_role_id_sql = "SELECT role_id FROM $user_role_table WHERE user_id='$user_id' AND location_id='$location_id'";
		$sql_result = api_sql_query($find_role_id_sql, __FILE__, __LINE__);
		$result = mysql_fetch_array($sql_result);
		$role_id = $result['role_id'];
		
		if (! isset($role_id) ) 
		{
			if (api_is_platform_admin()) $role_id = COURSE_ADMIN;
			else $role_id = ANONYMOUS_GUEST_COURSE_VISITOR;
		}
		
		return $role_id;
	}
	
		
	function get_multiple_course_local_user_role_id($user_id, $location_id_list)
	{
		if (count($location_id_list) == 0)
		{
			if (api_is_platform_admin()) $role_id = COURSE_ADMIN;
			else $role_id = ANONYMOUS_GUEST_COURSE_VISITOR;
			$role_id_list[] = $role_id;
		}
		else if (count($location_id_list) == 1)
		{
			$role_id_list[] = RolesRights::get_local_user_role_id_from_location_id($user_id, $location_id_list[0]);
		}
		else
		{
			echo "not implemented yet";
		}
		return $role_id_list;
	}
	
	/**
	* @return the translated role name of user $user_id in course $course_id
	* Currently this function takes only one local user role into account.
	*/
	function get_visual_local_user_role($user_id, $course_id)
	{
		$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
		$role = RolesRights::get_visual_role_name($role_id);
		return $role;
	}
	
	/**
	* This will certainly work for all locations inside a course:
	* the course itself, the course tools, subdivisions inside the tools...
	*/
	function get_course_location_id_from_short_location($location_part)
	{
		$location_table = Database::get_main_table(MAIN_LOCATION_TABLE);
		$find_location_id_sql = "SELECT id from $location_table WHERE location LIKE '%$location_part'";
		$sql_result = api_sql_query($find_location_id_sql, __FILE__, __LINE__);
		$result = mysql_fetch_array($sql_result);
		$location_id = $result['id'];
		return $location_id;
	}
	
	/**
	* This function will return info from the role-right-location table
	* for all sublocations of the $location_part parameter
	* The function first searches for %$location_part% in the list of locations,
	* then uses that list to look up info in the role-right-location table.
	*/
	function get_info_for_multiple_sublocations($location_part)
	{
		$location_table = Database::get_main_table(MAIN_LOCATION_TABLE);
		$relation_table = Database::get_main_table(MAIN_ROLE_RIGHT_LOCATION_TABLE);
		
		$find_location_id_sql = "SELECT id from $location_table WHERE location LIKE '%$location_part%'";
		$sql_result = api_sql_query($find_location_id_sql, __FILE__, __LINE__);
		while ($result = mysql_fetch_array($sql_result))
		{
			$location_info_list[] = $result;
		}
		
		foreach ($location_info_list as $location_info)
		{
			$sql_query = "SELECT * FROM $relation_table WHERE location_id='".$location_info['id']."'";
			$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
			while ($result = mysql_fetch_array($sql_result))
			{
				$complete_result_list[] = $result;
			}
		}
		return $complete_result_list;
	}
	
	/**
	* @return the character that separates parts of a location path
	* e.g. | is separator currently: platform|courses|course,TEST
	*/
	function get_location_path_separator()
	{
		return LOCATION_PATH_SEPARATOR;
	}
	
	function get_short_course_location_path($course_code)
	{
		return RolesRights::get_location_path_separator() . "course,$course_code";
	}
	
	function get_course_location_id($course_code)
	{
		$short_location = RolesRights::get_short_course_location_path($course_code);
		return RolesRights::get_course_location_id_from_short_location($short_location);
	}
	
	/**
	* Returns a list of location ids for all courses in the parameter list.
	* @param $course_list, an array with at least the 'code' field in every element.
	*/
	function get_multiple_course_location_id($course_list)
	{
		if (count($course_list) == 0) return false;
		else if (count($course_list) == 1)
		{
			$location_id_list[] = RolesRights::get_course_location_id($course_list[0]['code']);
		}
		else
		{
			//build location paths for all courses
			foreach($course_list as $this_course)
			{
				$course_code = $this_course['code'];
				$short_location_list[] = RolesRights::get_location_path_separator() . "course,$course_code";
			}
			//find the location ids of all courses using the location paths
			$course_search = "location LIKE '%".$short_location_list[0]."'";
			for($i = 1; $i < count($course_list); $i++)
			{
				$course_search .= " OR location LIKE '%".$short_location_list[$i]."' ";
			}	
			$location_table = Database::get_main_table(MAIN_LOCATION_TABLE);
			$find_location_id_sql = "SELECT id from $location_table WHERE $course_search";
			$sql_result = api_sql_query($find_location_id_sql, __FILE__, __LINE__);
			while($result = mysql_fetch_array($sql_result))
			{
				$location_id_list[] = $result['id'];
			}
		}
		return $location_id_list;
	}
	
	/**
	* @return location_id the id of the location of tool $tool_name in course $course_code
	*/
	function get_course_tool_location_id($course_code, $tool_name)
	{
		$short_location = RolesRights::get_short_course_location_path($course_code) . RolesRights::get_location_path_separator() . "tool,$tool_name";
		return RolesRights::get_course_location_id_from_short_location($short_location);
	}
	
	/**
	* Change the local role for user $user_id in location $location_id to $role_id.
	* This function assumes there is already an entry for the user.
	*/
	function set_user_local_role($user_id, $role_id, $location_id)
	{
		$user_role_table = Database::get_main_table(MAIN_USER_ROLE_TABLE);
		$sql_query = "UPDATE $user_role_table SET role_id='$role_id' WHERE user_id = '$user_id' AND location_id='$location_id'";
		api_sql_query($sql_query, __FILE__, __LINE__);
	}
	
	/**
	* Changes the value for a certain role, certain right in a certain location to
	* a new value.
	*
	* @param $role_id, the id of the role
	* @param $right_id, the id of the right
	* @param $location, the location
	* @param boolean $new_value, the new value to be stored
	*/
	function set_value($role_id, $right_id, $location_id, $new_value)
	{
		$relation_table = Database::get_main_table(MAIN_ROLE_RIGHT_LOCATION_TABLE);
		$sql_query = "SELECT * FROM $relation_table WHERE right_id='$right_id' AND  role_id='$role_id' AND location_id='$location_id'";
		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		if (mysql_num_rows($sql_result) > 0)
		{
			//an entry already exists, modify it
			$sql_query = "UPDATE $relation_table SET value='$new_value' WHERE right_id='$right_id' AND  role_id='$role_id' AND location_id='$location_id' ";
			$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		}
		else
		{
			//no entry exists yet, add one
			$sql_query = "INSERT INTO $relation_table (`role_id`, `right_id`, `location_id`, `value`) VALUES ('$role_id', '$right_id', '$location_id', '$new_value' )";
			$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		}
	}
	
	/**
	* Copies the values from the role-right-location table 
	* from one role to another, making their rights the same
	* for a specified location and all its sublocations.
	*/
	function copy_sublocation_values_role_to_role($from_role_id, $to_role_id, $right_id, $short_location)
	{
		$sublocations_info_list = RolesRights::get_info_for_multiple_sublocations($short_location);
		foreach($sublocations_info_list as $sublocation_info)
		{
			if ($sublocation_info['role_id'] == $from_role_id && $sublocation_info['right_id'] == $right_id)
			{
				RolesRights::set_value($to_role_id, $right_id, $sublocation_info['location_id'], $sublocation_info['value']);
			}
		}
	}
	
	/**
	* This function indicates wether someone with role $role_id is allowed to do
	* something that requires right $right_id in location $location_id.
	* The function takes the view_as_role option into account, but
	* for security reasons only teaching assistants and course admins are allowed to use this option.
	*
	* @return boolean true if the role has permission for that right in that location, false otherwise
	*/
	function is_allowed($role_id, $right_id, $location_id)
	{
		if ($role_id == TEACHING_ASSISTANT || $role_id == COURSE_ADMIN)
		{
			$view_as_role = $_SESSION['view_as_role'];
			//same view role selected as real role
			if ($role_id == $view_as_role) unset($_SESSION['view_as_role']);
			if (isset($view_as_role) && $view_as_role) $role_id = $view_as_role;
		}
		
		$relation_table = Database::get_main_table(MAIN_ROLE_RIGHT_LOCATION_TABLE);
		$sql_query = "SELECT * FROM $relation_table WHERE role_id='$role_id' AND right_id='$right_id' AND location_id='$location_id'";
		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
		if (mysql_num_rows($sql_result) > 0)
		{
			$result = mysql_fetch_array($sql_result);
			if ($result['value'] == '1') $return_value = true;
			else $return_value = false;
			return $return_value;
		}
		else return false;
	}
	
	/**
	* @return an array with as indices the constants VIEW_RIGHT, ADD_RIGHT,
	* EDIT_RIGHT and DELETE_RIGHT. These contain boolean values - true when
	* that right is allowed/set for for the specified role and location, false otherwise.
	*/
	function is_allowed_which_rights($role_id, $location_id)
	{
		$result[VIEW_RIGHT] = RolesRights::is_allowed($role_id, VIEW_RIGHT, $location_id);
		$result[EDIT_RIGHT] = RolesRights::is_allowed($role_id, EDIT_RIGHT, $location_id);
		$result[ADD_RIGHT] = RolesRights::is_allowed($role_id, ADD_RIGHT, $location_id);
		$result[DELETE_RIGHT] = RolesRights::is_allowed($role_id, DELETE_RIGHT, $location_id);
		return $result;
	}
	
	/**
	* This function protects a location, it is currently only tested
	* with courses and course tool locations.
	* The function checks wether the user's role has the view right on the specified location.
	* If no role is set, the user is assigned a guest role.
	* Inside courses, users who are not enrolled in the course typically have no local role specified
	* so they become guests.
	*/
	function protect_location($role_id, $location_id)
	{
		if (! isset($role_id))
		{
			$user_id = api_get_user_id();
			if (! isset($user_id)) $role_id = ANONYMOUS_GUEST_COURSE_VISITOR;
			else $role_id = REGISTERED_GUEST_COURSE_VISITOR;
		}
		$is_view_allowed = RolesRights::is_allowed($role_id, VIEW_RIGHT, $location_id);
		if ( ! $is_view_allowed) api_not_allowed();
	}
	
}
?>