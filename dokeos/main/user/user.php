<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) various contributors

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
*	This script displays a list of the users of the current course.
*	Course admins can change user perimssions, subscribe and unsubscribe users...
*
*	EXPERIMENTAL: support for virtual courses
*	- show users registered in virtual and real courses;
*	- only show the users of a virtual course if the current user;
*	is registered in that virtual course.
*
*	Exceptions: platform admin and the course admin will see all virtual courses.
*	This is a new feature, there may be bugs.
*
*	@todo possibility to edit user-course rights and view statistics for users in virtual courses
*	@todo convert normal table display to display function (refactor virtual course display function)
*	@todo display table functions need support for align and valign (e.g. to center text in cells) (this is now possible)
*	@author Roan Embrechts, refactoring + virtual courses support
*	@package dokeos.user
==============================================================================
*/
/*
==============================================================================
	   INIT SECTION
==============================================================================
*/
api_use_lang_files('registration');
include ("../inc/claro_init_global.inc.php");
$this_section = SECTION_COURSES;

// Roles and rights system
$user_id = api_get_user_id();
$course_id = api_get_course_id();
$role_id = RolesRights::get_local_user_role_id($user_id, $course_id);
$location_id = RolesRights::get_course_tool_location_id($course_id, TOOL_USER);
$is_allowed = RolesRights::is_allowed_which_rights($role_id, $location_id);

//block users without view right
RolesRights::protect_location($role_id, $location_id);

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/
include_once (api_get_library_path()."/debug.lib.inc.php");
include_once (api_get_library_path()."/events.lib.inc.php");
include_once (api_get_library_path()."/export.lib.inc.php");
include_once (api_get_library_path()."/course.lib.php");
require_once (api_get_library_path().'/sortabletable.class.php');
require_once (api_get_library_path().'/role_right.lib.php');

//CHECK KEYS
if (!isset ($_cid))
{
	header("location: ".$rootWeb);
}
/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
$currentCourseID = $_course['sysCode'];

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/**
 * Get the users to display on the current page.
 */
function get_number_of_users()
{
	$user_table = Database :: get_main_table(MAIN_USER_TABLE);
	$course_user_table = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
	$sql = "SELECT COUNT(u.user_id) AS number_of_users FROM $user_table u,$course_user_table cu WHERE u.user_id = cu.user_id and course_code='".$_SESSION['_course']['id']."'";
	if (isset ($_GET['keyword']))
	{
		$keyword = mysql_real_escape_string($_GET['keyword']);
		$sql .= " AND (firstname LIKE '%".$keyword."%' OR lastname LIKE '%".$keyword."%'  OR username LIKE '%".$keyword."%'  OR official_code LIKE '%".$keyword."%')";
	}
	$res = api_sql_query($sql, __FILE__, __LINE__);
	$result = mysql_fetch_object($res);
	return $result->number_of_users;
}

/**
 * Get the users to display on the current page.
 */
function get_user_data($from, $number_of_items, $column, $direction)
{
	global $is_allowed_to_track, $is_allowed;
	$user_table = Database :: get_main_table(MAIN_USER_TABLE);
	$course_user_table = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
	if($is_allowed[EDIT_RIGHT])
	{
		$columns[] = 'u.user_id';
	}
	$columns[] = 'u.official_code';
	$columns[] = 'u.lastname';
	$columns[] = 'u.firstname';
	$columns[] = 'cu.role';
	$columns[] = "''"; //placeholder for group-data
	if($is_allowed[EDIT_RIGHT])
	{
		//role column
		$columns[] = 'u.user_id';

		if($is_allowed_to_track)
		{
			$columns[] = 'u.user_id';
		}
	}
	$columns[] = 'u.user_id';

	$sql = "SELECT ";
	foreach( $columns as $index => $sqlcolumn)
	{
		$columns[$index] = ' '.$sqlcolumn.' AS col'.$index.' ';
	}
	$sql .= implode(" , ",$columns);
	$sql .= "FROM $user_table u,$course_user_table cu WHERE u.user_id = cu.user_id and course_code='".$_SESSION['_course']['id']."'";
	if (isset ($_GET['keyword']))
	{
		$keyword = mysql_real_escape_string($_GET['keyword']);
		$sql .= " AND (firstname LIKE '%".$keyword."%' OR lastname LIKE '%".$keyword."%'  OR username LIKE '%".$keyword."%'  OR official_code LIKE '%".$keyword."%')";
	}
	$sql .= " ORDER BY col$column $direction ";
	$sql .= " LIMIT $from,$number_of_items";
	$res = api_sql_query($sql, __FILE__, __LINE__);
	$users = array ();
	while ($user = mysql_fetch_row($res))
	{
		$users[''.$user[0]] = $user;
		$user_ids[] = $user[0];
	}
	$sql = "SELECT ug.user_id, ug.group_id group_id, sg.name
                    FROM ".Database :: get_course_group_user_table()." ug
                    LEFT JOIN ".Database :: get_course_group_table()." sg
                    ON ug.group_id = sg.id
                    WHERE ug.user_id IN ('".implode("','", $user_ids)."')";
    $res = api_sql_query($sql,__FILE__,__LINE__);
    while($group = mysql_fetch_object($res))
    {
    	$users[''.$group->user_id][5] .= $group->name.'<br />';
    }
	return $users;
}

/**
 * Build the tracking-column of the table
 * @param int $user_id The user id
 * @return string Some HTML-code
 */
function tracking_filter($user_id)
{
	global $origin;
	$result = '<a href="../tracking/userLog.php?'.api_get_cidreq().'&amp;origin='.$origin.'&amp;uInfo='.$user_id.'"><img border="0" alt="'.get_lang('Tracking').'" src="../img/statistics.png" /></a>';
	return $result;
}

/**
 * Build the modify-column of the table
 * @param int $user_id The user id
 * @return string Some HTML-code
 */
function modify_filter($user_id)
{
	global $origin, $_uid, $is_allowed;
	// info
	$result = '<a href="userInfo.php?origin='.$origin.'&amp;uInfo='.$user_id.'"><img border="0" alt="'.get_lang('Info').'" src="../img/info_small.gif" /></a>';
	if($is_allowed[EDIT_RIGHT])
	{
		// edit
		$result .= '<a href="userInfo.php?origin='.$origin.'&amp;editMainUserInfo='.$user_id.'"><img border="0" alt="'.get_lang('Edit').'" src="../img/edit.gif" /></a>';
		// unregister
		if ($user_id != $_uid)
		{
			$result .= '<a href="'.$_SERVER['PHP_SELF'].'?unregister=yes&amp;user_id_to_unregister='.$user_id.'&amp;'.$sort_params.'" onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang('ConfirmYourChoice'))).'\')) return false;"><img border="0" alt="'.get_lang("Unreg").'" src="../img/delete.gif"/></a>';
		}
	}
	return $result;
}

/**
* Returns a nice screen name of the local role of the user
* @param $user_id
*/
function role_filter($user_id)
{
	$course_id = api_get_course_id();
	return RolesRights::get_visual_local_user_role($user_id, $course_id);
}

/**
*	This function displays a list if users for each virtual course linked to the current
*	real course.
*
*	defines globals
*
*	@version 1.0
*	@author Roan Embrechts
*	@todo users from virtual courses always show "-" for the group related output. Edit and statistics columns are disabled *	for these users, for now.
*/
function show_users_in_virtual_courses($is_allowed_to_track, $origin='')
{
	global $_course, $_uid, $is_allowed;
	$real_course_code = $_course['sysCode'];
	$real_course_info = Database :: get_course_info_from_code($real_course_code);
	$user_subscribed_virtual_course_list = CourseManager :: get_list_of_virtual_courses_for_specific_user_and_real_course($_uid, $real_course_code);
	$number_of_virtual_courses = count($user_subscribed_virtual_course_list);
	$row = 0;
	$column_header[$row ++] = "ID";
	$column_header[$row ++] = get_lang("FullUserName");
	$column_header[$row ++] = get_lang("Role");
	$column_header[$row ++] = get_lang("Group");
	if ($is_allowed[EDIT_RIGHT])
		$column_header[$row ++] = get_lang('UserRole');
	if ($is_allowed_to_track)
		$column_header[$row++] = get_lang("Tracking");
	//$column_header[$row++] = get_lang("Edit");
	//$column_header[$row++] = get_lang("Unreg");
	if (!is_array($user_subscribed_virtual_course_list))
		return;
	foreach ($user_subscribed_virtual_course_list as $virtual_course)
	{
		$virtual_course_code = $virtual_course["code"];
		$virtual_course_user_list = CourseManager :: get_user_list_from_course_code($virtual_course_code);
		$message = get_lang("RegisteredInVirtualCourse")." ".$virtual_course["title"]."&nbsp;&nbsp;(".$virtual_course["code"].")";
		echo "<br/>";
		echo "<h4>".$message."</h4>";
		$properties["width"] = "100%";
		$properties["cellspacing"] = "1";
		Display :: display_complex_table_header($properties, $column_header);
		foreach ($virtual_course_user_list as $this_user)
		{
			$user_id = $this_user["user_id"];
			$loginname = $this_user["username"];
			$lastname = $this_user["lastname"];
			$firstname = $this_user["firstname"];
			$status = $this_user["status"];
			$description = $this_user["role"]; //is description, confusing name
			if ($status == "1")
				$status = get_lang("CourseManager");
			else
				$status = " - ";
			//if(xxx['tutor'] == '0') $tutor = " - ";
			//else  $tutor = get_lang("Tutor");
			$full_name = $lastname.", ".$firstname;
			if ($lastname == "" || $firstname == '')
				$full_name = $loginname;
			$user_info_hyperlink = "<a href=\"userInfo.php?".api_get_cidreq()."&origin=".$origin."&uInfo=".$user_id."&virtual_course=".$virtual_course["code"]."\">".$full_name."</a>";
			$row = 0;
			$table_row[$row ++] = $user_id;
			$table_row[$row ++] = $user_info_hyperlink; //Full name
			$table_row[$row ++] = $description;
			$table_row[$row ++] = get_group_list($user_id); //Group, still needs to be coded
			if ($is_allowed[EDIT_RIGHT])
			{
				$course_id = api_get_course_id();
				$role_name = RolesRights::get_visual_local_user_role($user_id, $virtual_course_code);
				$table_row[$row ++] = $role_name; //New role column
			}

			if ($is_allowed_to_track)
				$table_row[$row ++] = '<a href="../tracking/userLog.php?'.api_get_cidreq().'&amp;origin='.$origin.'&amp;uInfo='.$user_id.'"><img border="0" alt="'.get_lang('Tracking').'" src="../img/statistics.png" /></a>'; //Tracking column
			Display :: display_table_row($bgcolor, $table_row, true);
		}
		Display :: display_table_footer();
	}
}

/**
* Used for getting the group list of a user in a virtual course.
*/
function get_group_list($user_id)
{
	$sql = "SELECT ug.user_id, ug.group_id group_id, sg.name
                    FROM ".Database :: get_course_group_user_table()." ug
                    LEFT JOIN ".Database :: get_course_group_table()." sg
                    ON ug.group_id = sg.id
                    WHERE ug.user_id = $user_id";
    $res = api_sql_query($sql,__FILE__,__LINE__);
    while($group = mysql_fetch_object($res))
    {
    	$group_list .= $group->name.'<br />';
    }
    return $group_list;
}

/*
==============================================================================
		MAIN CODE
==============================================================================
*/
//statistics
event_access_tool(TOOL_USER);
/*
--------------------------------------
	Setting the permissions for this page
--------------------------------------
*/
$is_allowed_to_track = $is_allowed[EDIT_RIGHT] && $is_trackingEnabled;
/*
--------------------------------------
	Unregistering a user section
--------------------------------------
*/
if ($is_allowed[EDIT_RIGHT])
{
	if (isset ($_POST['action']))
	{
		switch ($_POST['action'])
		{
			case 'unsubscribe' :
				if (count($_POST['user']) > 0)
				{
					$user_ids = implode(",", $_POST['user']);
					$sql = "DELETE FROM ".Database :: get_main_table(MAIN_COURSE_USER_TABLE)." WHERE user_id IN (".$user_ids.") AND user_id != ".$_uid." AND course_code = '$currentCourseID'";
					api_sql_query($sql, __FILE__, __LINE__);
					$sql = "DELETE FROM ".Database :: get_course_table(GROUP_USER_TABLE)." WHERE user_id IN (".$user_ids.")";
					api_sql_query($sql, __FILE__, __LINE__);
				}
				break;
		}
	}
	if (isset ($_GET['action']))
	{
		switch ($_GET['action'])
		{
			case 'export' :
				$table_user = Database :: get_main_table(MAIN_USER_TABLE);
				$table_course_user = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
				$course = api_get_course_info();
				$sql = "SELECT official_code,firstname,lastname,email FROM $table_user u, $table_course_user cu WHERE cu.user_id = u.user_id AND cu.course_code = '".$course['sysCode']."' ORDER BY lastname ASC";
				$users = api_sql_query($sql, __FILE__, __LINE__);
				while ($user = mysql_fetch_array($users, MYSQL_ASSOC))
				{
					$data[] = $user;
				}
				switch ($_GET['type'])
				{
					case 'csv' :
						Export :: export_table_csv($data);
					case 'xls' :
						Export :: export_table_xls($data);
				}

		}
	}
	// Unregister user from course
	// (notice : it does not delete user from Dokeos main DB)
	if ($_GET['unregister'])
	{
		$user_id_to_unregister = $_GET['user_id_to_unregister'];
		CourseManager::unsubscribe_user($user_id_to_unregister, $course_id);
	}
} // end if allowed to edit

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/
if ($origin != 'learnpath')
{
	//so we are not in learnpath tool
	$nameTools = get_lang("Users");
	Display :: display_header($nameTools, "User");
}
else
{
?> <link rel="stylesheet" type="text/css" href="<?php echo api_get_path(WEB_CODE_PATH); ?>css/default.css" /> <?php


}

/*
1. since only a count is used there is not need to use the MAIN_USER_TABLE
SELECT count(user_id) nb_users FROM  ".Database :: get_main_table(MAIN_COURSE_USER_TABLE)." WHERE course_code=".$currentCourseID
2. secondly $userTotalNb is not used anywhere in the code but the SQL statement is executed
So we have double performance loss:
* using a table that is irrelevant in the query
* executing a statement that is not used.
*/

/*
$sqlNbUser = "SELECT count(user.user_id) nb_users
              FROM ".Database :: get_main_table(MAIN_COURSE_USER_TABLE)." cu,
                   ".Database :: get_main_table(MAIN_USER_TABLE)." `user`
              WHERE	course_code = '".$currentCourseID."'
              AND cu.user_id = `user`.user_id";
$result = api_sql_query($sqlNbUser);
$userTotalNb = mysql_fetch_array($result, MYSQL_ASSOC);
$userTotalNb = $userTotalNb["nb_users"];
//count is currently not correct because it does not take virtual courses into account
//so I've temporarily disabled it.
//echo "<h3>$nameTools (".get_lang('UserNumber')." : $userTotalNb)</h3>";
*/
api_display_tool_title($nameTools);


if ($is_allowed[EDIT_RIGHT])
{
	echo "<div align=\"right\">";
	echo '<a href="user.php?action=export&type=csv">'.get_lang('ExportAsCSV').'</a> | ';
	echo '<a href="user.php?action=export&type=xls">'.get_lang('ExportAsXLS').'</a> | ';
	echo "<a href=\"subscribe_user.php\">".get_lang("SubscribeUserToCourse")."</a> | ";
	echo "<a href=\"../group/group.php?".api_get_cidreq()."\">".get_lang("GroupUserManagement")."</a>";
	echo "</div>";
}
/*
--------------------------------------
	DISPLAY USERS LIST
--------------------------------------
	Also shows a "next page" button if there are
	more than 50 users.

	There's a bug in here somewhere - some users count as more than one if they are in more than one group
	--> code for > 50 users should take this into account
	(Roan, Feb 2004)
*/
if (CourseManager::has_virtual_courses_from_code($course_id, $user_id))
{
	$real_course_code = $_course['sysCode'];
	$real_course_info = Database :: get_course_info_from_code($real_course_code);
	$message = get_lang("RegisteredInRealCourse")." ".$real_course_info["title"]."&nbsp;&nbsp;(".$real_course_info["official_code"].")";
	echo "<h4>".$message."</h4>";
}

/*
==============================================================================
		DISPLAY LIST OF USERS
==============================================================================
*/

$default_column = $is_allowed[EDIT_RIGHT] ? 2 : 1;
$table = new SortableTable('users', 'get_number_of_users', 'get_user_data',$default_column);
$parameters['keyword'] = $_GET['keyword'];
$table->set_additional_parameters($parameters);
$header_nr = 0;
if ($is_allowed[EDIT_RIGHT])
{
	$table->set_header($header_nr++, '', false);
}
$table->set_header($header_nr++, get_lang('OfficialCode'));
$table->set_header($header_nr++, get_lang('Lastname'));
$table->set_header($header_nr++, get_lang('Firstname'));
$table->set_header($header_nr++, get_lang('Role'));
$table->set_header($header_nr++, get_lang('Group'),false);
if ($is_allowed[EDIT_RIGHT])
{
	//disabled for 1.7
	//separate columns are merged into one role column
	//$table->set_header($header_nr++, get_lang('Tutor'));
	//$table->set_header($header_nr++, get_lang('CourseManager'));

	//roles column (new for 1.7)
	$table->set_header($header_nr++, get_lang('UserRole'), true);
	$table->set_column_filter($header_nr-1,'role_filter');

	if ($is_allowed_to_track)
	{
		$table->set_header($header_nr++, get_lang('Tracking'), false);
		$table->set_column_filter($header_nr-1,'tracking_filter');
	}
}

//actions column
$table->set_header($header_nr++, '', false);
$table->set_column_filter($header_nr-1,'modify_filter');
if ($is_allowed[EDIT_RIGHT])
{
	$table->set_form_actions(array ('unsubscribe' => get_lang('Unreg')), 'user');
}

// Build search-form
$form = new FormValidator('search_user', 'get','','',null,false);
$renderer = & $form->defaultRenderer();
$renderer->setElementTemplate('<span>{element}</span> ');
$form->add_textfield('keyword', '', false);
$form->addElement('submit', 'submit', get_lang('SearchButton'));

$form->display();
echo '<br />';
$table->display();
if (api_get_setting('allow_user_headings') == 'true' && $is_allowed[EDIT_RIGHT] && $origin != 'learnpath') // only course administrators see this line
{
	echo "<div align=\"right\">", "<form method=\"post\" action=\"userInfo.php\">", get_lang("CourseAdministratorOnly"), " : ", "<input type=\"submit\" name=\"viewDefList\" value=\"".get_lang("DefineHeadings")."\" />", "</form>", "</div>\n";
}

//User list of the virtual courses linked to this course.
show_users_in_virtual_courses($is_allowed_to_track);

/*
==============================================================================
		FOOTER
==============================================================================
*/
if ($origin != 'learnpath')
{
	Display :: display_footer();
}
?>