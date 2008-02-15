<?php
//  $Id$
/*
============================================================================== 
	Dokeos - elearning and course management software
	
	Copyright (c) 2004-2005 Dokeos S.A.
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
*	This script allows teachers to subscribe existing classes to their course.
*	@package dokeos.user
============================================================================== 
*/
/*
============================================================================== 
		INIT SECTION
============================================================================== 
*/
api_use_lang_files('registration');
include ("../inc/global.inc.php");
$this_section = SECTION_COURSES;

require_once (api_get_library_path().'/course.lib.php');
require_once (api_get_library_path().'/sortabletable.class.php');
require_once (api_get_library_path().'/classmanager.lib.php');
require_once (api_get_library_path().'/formvalidator/FormValidator.class.php');

/*
============================================================================== 
		MAIN CODE
============================================================================== 
*/

api_protect_course_script();

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/
$tool_name = get_lang("AddClassesToACourse");
//extra entries in breadcrumb
$interbredcrump[] = array ("url" => "user.php", "name" => get_lang("Users"));
Display :: display_header($tool_name, "User");

api_display_tool_title($tool_name);

/*
============================================================================== 
		MAIN SECTION
============================================================================== 
*/

if (isset ($_GET['register']))
{
	ClassManager::subscribe_to_course($_GET['class_id'],$_course['sysCode']);
	Display::display_normal_message(get_lang('ClassesSubscribed'));
}
if (isset ($_POST['action']))
{
	switch ($_POST['action'])
	{
		case 'subscribe' :
			if (is_array($_POST['class']))
			{
				foreach ($_POST['class'] as $index => $class_id)
				{
					ClassManager::subscribe_to_course($class_id,$_course['sysCode']);
				}
				Display::display_normal_message(get_lang('ClassesSubscribed'));
			}
			break;
	}
}

/*
-----------------------------------------------------------
		SHOW LIST OF USERS
-----------------------------------------------------------
*/

/**
 *  * Get the classes to display on the current page.
 */
function get_number_of_classes()
{
	$class_table = Database :: get_main_table(MAIN_CLASS_TABLE);
	$course_class_table = Database :: get_main_table(MAIN_COURSE_CLASS_TABLE);
	$sql = "SELECT c.id	FROM $class_table c
						";
						/* #LEFT JOIN $course_user_table cu on u.user_id = cu.user_id and course_code='".$_SESSION['_course']['id']." '
						*/
	if (isset ($_GET['keyword']))
	{
		$keyword = mysql_real_escape_string($_GET['keyword']);
		$sql .= " WHERE (c.name LIKE '%".$keyword."%')";
	}
	$res = api_sql_query($sql, __FILE__, __LINE__);
	$result = mysql_num_rows($res);
	return $result;
}
/**
 * Get the classes to display on the current page.
 */
function get_class_data($from, $number_of_items, $column, $direction)
{
	$class_table = Database :: get_main_table(MAIN_CLASS_TABLE);
	$course_class_table = Database :: get_main_table(MAIN_COURSE_CLASS_TABLE);
	$class_user_table = Database :: get_main_table(MAIN_CLASS_USER_TABLE);
	$sql = "SELECT 
							c.id AS col0,
							c.name   AS col1, 
							COUNT(cu.user_id) AS col2,
							c.id AS col3
						FROM $class_table c
						";
	$sql .= " LEFT JOIN $class_user_table cu ON cu.class_id = c.id";

	if (isset ($_GET['keyword']))
	{
		$keyword = mysql_real_escape_string($_GET['keyword']);
		$sql .= " WHERE (c.name LIKE '%".$keyword."%')";
	}
	$sql .= " GROUP BY c.id, c.name ";
	$sql .= " ORDER BY col$column $direction ";
	$sql .= " LIMIT $from,$number_of_items";
	$res = api_sql_query($sql, __FILE__, __LINE__);
	$classes = array ();
	while ($class = mysql_fetch_row($res))
	{
		$classes[] = $class;
	}
	return $classes;
}
/**
 * Build the reg-column of the table
 * @param int $class_id The class id 
 * @return string Some HTML-code
 */
function reg_filter($class_id)
{
	$result = "<a href=\"".$_SERVER['PHP_SELF']."?register=yes&amp;class_id=".$class_id."\">".get_lang("reg")."</a>";
	return $result;
}
// Build search-form
$form = new FormValidator('search_class', 'get','','',null,false);
$renderer = $form->defaultRenderer();
$renderer->setElementTemplate('<span>{element}</span> ');
$form->add_textfield('keyword', '', false);
$form->addElement('submit', 'submit', get_lang('SearchButton'));

// Build table
$table = new SortableTable('users', 'get_number_of_classes', 'get_class_data', 1);
$parameters['keyword'] = $_GET['keyword'];
$table->set_additional_parameters($parameters);
$col = 0;
$table->set_header($col ++, '', false);
$table->set_header($col ++, get_lang('ClassName'));
$table->set_header($col ++, get_lang('NumberOfUsers'));
$table->set_header($col ++, get_lang('reg'), false);
$table->set_column_filter($col -1, 'reg_filter');
$table->set_form_actions(array ('subscribe' => get_lang('reg')), 'class');

// Display form & table
$form->display();
echo '<br />';
$table->display();
/*
============================================================================== 
		FOOTER 
============================================================================== 
*/
Display :: display_footer();
?>