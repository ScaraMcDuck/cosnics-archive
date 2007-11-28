<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
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
* This is the course creation library for Dokeos.
* It contains functions to create a course.
* Include/require it in your code to use its functionality.
*
* @package dokeos.library
* @todo clean up horrible structure, script is unwieldy, for example easier way to deal with
* different tool visibility settings: ALL_TOOLS_INVISIBLE, ALL_TOOLS_VISIBLE, CORE_TOOLS_VISIBLE...
==============================================================================
*/

include_once (api_get_library_path().'/database.lib.php');

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/**
* Add values for the new course to the table that holds the
* role - right - location relation.
*
* All rights for the new course are copied from a default set of course rights,
* which the platform admin can edit.
* So for every newly created course, the value of the location / right / role combination is
* - at the start at least - the exact same value as the location / right / role combination
* for the default course.
*/
function add_course_role_right_location_values($course_code)
{
	$location_table = Database::get_main_table(MAIN_LOCATION_TABLE);
	$relation_table = Database::get_main_table(MAIN_ROLE_RIGHT_LOCATION_TABLE);

	//get default course locations, store into $default_location_list array
	$course_location = "|course,DEFAULT";
	$get_default_location_sql = "SELECT * FROM $location_table WHERE ( location LIKE '%$course_location|%' ) OR ( location LIKE '%$course_location' )";

	$sql_result = api_sql_query($get_default_location_sql);
	while ($result = mysql_fetch_array($sql_result, MYSQL_ASSOC) )
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
		api_sql_query($add_new_location_sql);
		$new_location_id = mysql_insert_id();

		//find all relations in the table that contained the default location id
		$find_relations_query = "SELECT * FROM $relation_table WHERE location_id='".$default_location['id']."'";
		$sql_result = api_sql_query($find_relations_query);
		while ($relation_result = mysql_fetch_array($sql_result, MYSQL_ASSOC) )
		{
			//insert new relation with same role id, same right id, new location id, and same value
			$add_new_relation_sql = "INSERT INTO ".$relation_table." VALUES ('".$relation_result['role_id']."', '".$relation_result['right_id']."', '$new_location_id', '".$relation_result['value']."')";
			api_sql_query($add_new_relation_sql);
		}
	}
}
?>