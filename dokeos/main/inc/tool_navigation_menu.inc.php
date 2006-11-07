<?php

// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2005 Dokeos S.A.
	Copyright (c) 2004-2005 Sandra Mathijs, Hogeschool Gent
	Copyright (c) 2005 Roan Embrechts, Vrije Universiteit Brussel
	Copyright (c) 2005 Wolfgang Schneider
	Copyright (c) Bart Mollet, Hogeschool Gent

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
*	Navigation menu display code
*
*	@package dokeos.include
==============================================================================
*/
/**
 * Build the navigation items to show in a course menu
 * @param boolean $include_admin_tools
 */
function get_navigation_items($include_admin_tools = false)
{
	$navigation_items = array ();
	$course_id = api_get_course_id();

	if (isset ($course_id))
	{
		//roles and rights system for 1.7
		$user_id = api_get_user_id();
		$role_id = RolesRights :: get_local_user_role_id($user_id, $course_id);
		$location_id = RolesRights :: get_course_location_id($course_id);
		$is_allowed = RolesRights :: is_allowed_which_rights($role_id, $location_id);

		$course_tools_table = Database :: get_course_table(TOOL_LIST_TABLE);

		/*
		--------------------------------------------------------------
					Link to the Course homepage
		--------------------------------------------------------------
		*/

		$navigation_items['home']['image'] = 'home.gif';
		$navigation_items['home']['link'] = api_get_path(REL_COURSE_PATH).$_SESSION['_course']['path'].'/index.php';
		$navigation_items['home']['name'] = get_lang("CourseHomepageLink");

		/*
		--------------------------------------------------------------
					Link to the different tools
		--------------------------------------------------------------
		*/

		$sql_menu_query = "SELECT * FROM $course_tools_table WHERE visibility='1' and admin='0' ORDER BY id ASC";
		$sql_result = api_sql_query($sql_menu_query, __FILE__, __LINE__);
		while ($row = mysql_fetch_array($sql_result))
		{
			$navigation_items[$row['id']] = $row;
			if (!stristr($row['link'], 'http://'))
			{
				$navigation_items[$row['id']]['link'] = api_get_path(REL_CLARO_PATH).$row['link'];
				$navigation_items[$row['id']]['name'] = $row['image'] == 'scormbuilder.gif' ? $navigation_items[$row['id']]['name'] : get_lang($navigation_items[$row['id']]['name']);
			}
		}
		/*
		--------------------------------------------------------------
			Admin (edit rights) only links
			- Course settings (course admin only)
			- Course rights (roles & rights overview)
		--------------------------------------------------------------
		*/

		if ($is_allowed[EDIT_RIGHT] && $include_admin_tools)
		{

			$course_settings_sql = "	SELECT name,image FROM $course_tools_table
															WHERE link='course_info/infocours.php'";
			$course_rights_sql = "		SELECT name,image FROM $course_tools_table
															WHERE link='course_info/course_rights.php'";
			$sql_result = api_sql_query($course_settings_sql);
			$course_setting_info = mysql_fetch_array($sql_result);
			$course_setting_visual_name = get_lang($course_setting_info['name']);
			$sql_result = api_sql_query($course_rights_sql);
			$course_rights_info = mysql_fetch_array($sql_result);
			$course_rights_visual_name = get_lang($course_rights_info['name']);

			// course settings item
			$navigation_items['course_settings']['image'] = $course_setting_info['image'];
			$navigation_items['course_settings']['link'] = api_get_path(REL_CLARO_PATH).'course_info/infocours.php';
			$navigation_items['course_settings']['name'] = $course_setting_visual_name;

			// course rights item
			$navigation_items['roles_rights']['image'] = $course_rights_info['image'];
			$navigation_items['roles_rights']['link'] = api_get_path(REL_CLARO_PATH).'course_info/course_rights.php';
			$navigation_items['roles_rights']['name'] = $course_rights_visual_name;

		}
	}
	foreach($navigation_items as $key => $navigation_item)
	{
		if (strstr($navigation_item['link'], '?'))
		{
			//link already contains a parameter, add course id parameter with &
			$parameter_separator = "&";
		}
		else
		{
			//link doesn't contain a parameter yet, add course id parameter with ?
			$parameter_separator = "?";
		}
		$navigation_items[$key]['link'] .= $parameter_separator.api_get_cidreq();
	}
	return $navigation_items;
}
/**
 * Show a navigation menu
 */
function show_navigation_menu()
{
	$navigation_items = get_navigation_items(true);
	echo '<div id="toolnavbox"><div id="toolnavlist"><dl>';
	foreach ($navigation_items as $key => $navigation_item)
	{
		echo '<dd>';
		if (api_get_setting('show_icons_in_navigation_menu') == 'true')
		{
			echo '<img src="'.api_get_path(WEB_CODE_PATH).'img/'.$navigation_item['image'].'" alt="'.$navigation_item['name'].'"/>';
		}
		$url_item = parse_url($navigation_item['link']);
		$url_current = parse_url($_SERVER['REQUEST_URI']);
		echo '<a href="'.$navigation_item['link'].'"';
		if ($url_item['path'] == $url_current['path'])
		{
			if(! isset($_GET['learnpath_id']) || strpos($url_item['query'],'learnpath_id='.$_GET['learnpath_id']) === 0)
			{
				echo ' id="here"';
			}
		}
		echo '>';
		echo $navigation_item['name'];
		echo '</a>';
		echo '</dd>';
		echo "\n";
	}
	echo '</dl></div></div>';
}
/**
 * Show a toolbar with shortcuts to the course tool
 */
function show_navigation_tool_shortcuts()
{
	$navigation_items = get_navigation_items(false);
	foreach ($navigation_items as $key => $navigation_item)
	{
		echo '<a href="'.$navigation_item['link'].'"';
		if (strpos($_SERVER['PHP_SELF'], $navigation_item['link']) !== false)
		{
			echo ' id="here"';
		}
		echo ' target="_top" title="'.$navigation_item['name'].'">';
		echo '<img src="'.api_get_path(WEB_CODE_PATH).'img/'.$navigation_item['image'].'" alt="'.$navigation_item['name'].'"/>';
		echo '</a>';
	}
}
?>