<?php

// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert

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
	@author Bart Mollet
*	@package dokeos.admin
==============================================================================
*/


api_use_lang_files('admin');
$cidReset = true;
require ('../inc/global.inc.php');
require_once (api_get_library_path().'/sortabletable.class.php');
require_once (api_get_library_path().'/formvalidator/FormValidator.class.php');
$this_section = SECTION_PLATFORM_ADMIN;

api_protect_admin_script();
/**
*	Make sure this function is protected
*	because it does NOT check password!
*
*	This function defines globals.
*	@author Roan Embrechts
*/
function login_user($user_id)
{
	//init ---------------------------------------------------------------------
	global $_uid, $uidReset, $loginFailed, $uidReset, $is_trackingEnabled, $_user;
	global $is_platformAdmin, $is_allowedCreateCourse;

	$main_user_table = Database :: get_main_table(MAIN_USER_TABLE);
	$main_admin_table = Database :: get_main_table(MAIN_ADMIN_TABLE);
	$track_e_login_table = Database :: get_statistic_table(STATISTIC_TRACK_E_LOGIN_TABLE);

	//logic --------------------------------------------------------------------
	//unset($_uid); // uid not in session ? prevent any hacking

	if (!isset ($user_id))
	{
		$uidReset = true;
		return;
	}

	$sql_query = "SELECT * FROM $main_user_table WHERE user_id='$user_id'";
	$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);
	$result = Database :: fetch_array($sql_result);

	$firstname = $result["firstname"];
	$lastname = $result["lastname"];
	$user_id = $result["user_id"];

	$message = "Attempting to login as ".$firstname." ".$lastname." (id ".$user_id.")";

	$_uid = $user_id;
	//bug: this only works if $_uid is global
	api_session_register('_uid');

	$loginFailed = false;
	$uidReset = false;

	if ($user_id) // a uid is given (log in succeeded)
	{
		if ($is_trackingEnabled)
		{
			$sql_query = "SELECT user.*, a.user_id is_admin,
				UNIX_TIMESTAMP(login.login_date) login_date
				FROM $main_user_table
				LEFT JOIN $main_admin_table a
				ON user.user_id = a.user_id
				LEFT JOIN $track_e_login_table login
				ON user.user_id = login.login_user_id
				WHERE user.user_id = '$_uid'
				ORDER BY login.login_date DESC LIMIT 1";
		}
		else
		{
			$sql_query = "SELECT user.*, a.user_id is_admin
				FROM $main_user_table
				LEFT JOIN $main_admin_table a
				ON user.user_id = a.user_id
				WHERE user.user_id = '$_uid'";
		}

		$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);

		if (mysql_num_rows($sql_result) > 0)
		{
			// Extracting the user data

			$user_data = mysql_fetch_array($sql_result);

			$_user['firstName'] = $user_data['firstname'];
			$_user['lastName'] = $user_data['lastname'];
			$_user['mail'] = $user_data['email'];
			$_user['lastLogin'] = $user_data['login_date'];
			$_user['official_code'] = $user_data['official_code'];
			$_user['picture_uri'] = $user_data['picture_uri'];

			$is_platformAdmin = (bool) (!is_null($user_data['is_admin']));
			$is_allowedCreateCourse = (bool) ($user_data['status'] == 1);

			//bug: this only works if $_user is global
			api_session_register('_user');
			api_session_register('is_platformAdmin');
			api_session_register('is_allowedCreateCourse');

			$target_url = api_get_path(WEB_PATH)."index.php";
			$message .= "<br/>Login successful. Go to <a href=\"$target_url\">$target_url</a>";
			Display :: display_header(get_lang('UserList'));
			Display :: display_normal_message($message);
			Display :: display_footer();
			exit;
		}
		else
		{
			exit ("<br/>WARNING UNDEFINED UID !! ");
		}
	}
}
/**
 * Get the total number of users on the platform
 * @see SortableTable#get_total_number_of_items()
 */
function get_number_of_users()
{
	$user_table = Database :: get_main_table(MAIN_USER_TABLE);
	$sql = "SELECT COUNT(user_id) AS total_number_of_items FROM $user_table";
	if (isset ($_GET['keyword']))
	{
		$keyword = mysql_real_escape_string($_GET['keyword']);
		$sql .= " WHERE firstname LIKE '%".$keyword."%' OR lastname LIKE '%".$keyword."%'  OR email LIKE '%".$keyword."%'  OR official_code LIKE '%".$keyword."%'";
	}
	elseif (isset ($_GET['keyword_firstname']))
	{
		$keyword_firstname = mysql_real_escape_string($_GET['keyword_firstname']);
		$keyword_lastname = mysql_real_escape_string($_GET['keyword_lastname']);
		$keyword_email = mysql_real_escape_string($_GET['keyword_email']);
		$keyword_username = mysql_real_escape_string($_GET['keyword_username']);
		$keyword_status = mysql_real_escape_string($_GET['keyword_status']);
		$sql .= " WHERE firstname LIKE '%".$keyword_firstname."%' AND lastname LIKE '%".$keyword_lastname."%' AND username LIKE '%".$keyword_username."%'  AND email LIKE '%".$keyword_email."%'   AND official_code LIKE '%".$keyword_officialcode."%'    AND status LIKE '".$keyword_status."'";
	}
	$res = api_sql_query($sql, __FILE__, __LINE__);
	$obj = mysql_fetch_object($res);
	return $obj->total_number_of_items;
}
/**
 * Get the users to display on the current page.
 * @see SortableTable#get_table_data($from)
 */
function get_user_data($from, $number_of_items, $column, $direction)
{
	$user_table = Database :: get_main_table(MAIN_USER_TABLE);
	$sql = "SELECT
                 user_id			AS col0,
                 official_code		AS col1,
                 lastname 			AS col2,
                 firstname 			AS col3,
                 username			AS col4,
                 email				AS col5,
                 IF(status=1,'".get_lang('Teacher')."','".get_lang('Student')."')	 AS col6,
                 user_id			AS col7
             FROM
                 $user_table ";
	if (isset ($_GET['keyword']))
	{
		$keyword = mysql_real_escape_string($_GET['keyword']);
		$sql .= " WHERE firstname LIKE '%".$keyword."%' OR lastname LIKE '%".$keyword."%'  OR username LIKE '%".$keyword."%'  OR official_code LIKE '%".$keyword."%'";
	}
	elseif (isset ($_GET['keyword_firstname']))
	{
		$keyword_firstname = mysql_real_escape_string($_GET['keyword_firstname']);
		$keyword_lastname = mysql_real_escape_string($_GET['keyword_lastname']);
		$keyword_email = mysql_real_escape_string($_GET['keyword_email']);
		$keyword_username = mysql_real_escape_string($_GET['keyword_username']);
		$keyword_status = mysql_real_escape_string($_GET['keyword_status']);
		$sql .= " WHERE firstname LIKE '%".$keyword_firstname."%' AND lastname LIKE '%".$keyword_lastname."%' AND username LIKE '%".$keyword_username."%'  AND email LIKE '%".$keyword_email."%'   AND official_code LIKE '%".$keyword_officialcode."%'    AND status LIKE '".$keyword_status."'";
	}
	$sql .= " ORDER BY col$column " . ($direction == SORT_DESC ? 'DESC' : 'ASC');
	$sql .= " LIMIT $from,$number_of_items";
	$res = api_sql_query($sql, __FILE__, __LINE__);
	$users = array ();
	while ($user = mysql_fetch_row($res))
	{
		$users[] = $user;
	}
	return $users;
}
/**
* Returns a mailto-link
* @param string $email An email-address
* @return string HTML-code with a mailto-link
*/
function email_filter($email)
{
	return Display :: encrypted_mailto_link($email, $email);
}
/**
 * Build the modify-column of the table
 * @param int $user_id The user id
 * @param string $url_params
 * @return string Some HTML-code with modify-buttons
 */
function modify_filter($user_id,$url_params)
{
	$result = '<a href="user_information.php?user_id='.$user_id.'"><img src="../img/info_small.gif" border="0" style="vertical-align: middle;" title="'.get_lang('Info').'" alt="'.get_lang('Info').'"/></a>';
	$result .= '<a href="user_list.php?action=login_as&amp;user_id='.$user_id.'"><img src="../img/loginas.gif" border="0" style="vertical-align: middle;" alt="'.get_lang('LoginAs').'" title="'.get_lang('LoginAs').'"/></a>';
	$result .= '<a href="user_edit.php?user_id='.$user_id.'"><img src="../img/edit.gif" border="0" style="vertical-align: middle;" title="'.get_lang('Edit').'" alt="'.get_lang('Edit').'"/></a>';
	$result .= '<a href="user_list.php?action=delete_user&amp;user_id='.$user_id.'&amp;'.$url_params.'"  onclick="javascript:if(!confirm('."'".addslashes(htmlentities(get_lang("ConfirmYourChoice")))."'".')) return false;"><img src="../img/delete.gif" border="0" style="vertical-align: middle;" title="'.get_lang('Delete').'" alt="'.get_lang('Delete').'"/></a>';
	return $result;
}
/**
==============================================================================
		INIT SECTION
==============================================================================
*/
require_once (api_get_library_path().'/usermanager.lib.php');
$action = $_GET["action"];
$login_as_user_id = $_GET["user_id"];

if ($action == "login_as" && isset ($login_as_user_id))
{
	login_user($login_as_user_id);
}

if (isset ($_GET['search']) && $_GET['search'] == 'advanced')
{
	$interbredcrump[] = array ("url" => "index.php", "name" => get_lang('PlatformAdmin'));
	$interbredcrump[] = array ("url" => 'user_list.php', "name" => get_lang('UserList'));
	$tool_name = get_lang('SearchAUser');
	Display :: display_header($tool_name);
	api_display_tool_title($tool_name);
	$form = new FormValidator('advanced_search','get');
	$form->add_textfield('keyword_firstname',get_lang('FirstName'),false);
	$form->add_textfield('keyword_lastname',get_lang('LastName'),false);
	$form->add_textfield('keyword_username',get_lang('LoginName'),false);
	$form->add_textfield('keyword_email',get_lang('Email'),false);
	$form->add_textfield('keyword_officialcode',get_lang('OfficialCode'),false);
	$status_options = array();
	$status_options['%'] = get_lang('All');
	$status_options[STUDENT] = get_lang('Student');
	$status_options[COURSEMANAGER] = get_lang('Teacher');
	$form->addElement('select','keyword_status',get_lang('Status'),$status_options);
	$form->addElement('submit','submit',get_lang('Ok'));
	$form->display();
}
else
{
	$interbredcrump[] = array ("url" => "index.php", "name" => get_lang('PlatformAdmin'));
	$tool_name = get_lang('UserList');
	Display :: display_header($tool_name, "");
	api_display_tool_title($tool_name);
	if (isset ($_GET['action']))
	{
		switch ($_GET['action'])
		{
			case 'show_message' :
				Display :: display_normal_message(stripslashes($_GET['message']));
				break;
			case 'delete_user' :
				if ($user_id != $_uid && UserManager :: delete_user($_GET['user_id']))
				{
					Display :: display_normal_message(get_lang('UserDeleted'));
				}
				else
				{
					Display :: display_error_message(get_lang('CannotDeleteUser'));
				}
				break;
		}
	}
	if (isset ($_POST['action']))
	{
		switch ($_POST['action'])
		{
			case 'delete' :
				$number_of_selected_users = count($_POST['id']);
				$number_of_deleted_users = 0;
				foreach ($_POST['id'] as $index => $user_id)
				{
					if($user_id != $_uid)
					{
						if(UserManager :: delete_user($user_id))
						{
							$number_of_deleted_users++;
						}
					}
				}
				if($number_of_selected_users == $number_of_deleted_users)
				{
					Display :: display_normal_message(get_lang('SelectedUsersDeleted'));
				}
				else
				{
					Display :: display_error_message(get_lang('SomeUsersNotDeleted'));
				}
				break;
		}
	}
	// Create a search-box
	$form = new FormValidator('search_simple','get','','',null,false);
	$renderer =& $form->defaultRenderer();
	$renderer->setElementTemplate('<span>{element}</span> ');
	$form->addElement('text','keyword',get_lang('keyword'));
	$form->addElement('submit','submit',get_lang('Search'));
	$form->addElement('static','search_advanced_link',null,'<a href="user_list.php?search=advanced">'.get_lang('AdvancedSearch').'</a>');
	$form->display();
	if (isset ($_GET['keyword']))
	{
		$parameters = array ('keyword' => $_GET['keyword']);
	}
	elseif (isset ($_GET['keyword_firstname']))
	{
		$parameters['keyword_firstname'] = $_GET['keyword_firstname'];
		$parameters['keyword_lastname'] = $_GET['keyword_lastname'];
		$parameters['keyword_email'] = $_GET['keyword_email'];
		$parameters['keyword_officialcode'] = $_GET['keyword_officialcode'];
		$parameters['keyword_status'] = $_GET['keyword_status'];
	}
	// Create a sortable table with user-data
	$table = new SortableTable('users', 'get_number_of_users', 'get_user_data',2);
	$table->set_additional_parameters($parameters);
	$table->set_header(0, '', false);
	$table->set_header(1, get_lang('OfficialCode'));
	$table->set_header(2, get_lang('LastName'));
	$table->set_header(3, get_lang('FirstName'));
	$table->set_header(4, get_lang('LoginName'));
	$table->set_header(5, get_lang('Email'));
	$table->set_header(6, get_lang('Status'));
	$table->set_header(7, '', false);
	$table->set_column_filter(5, 'email_filter');
	$table->set_column_filter(7, 'modify_filter');
	$table->set_form_actions(array ('delete' => get_lang('DeleteFromPlatform')));
	$table->display();
}
/*
==============================================================================
		FOOTER
==============================================================================
*/
Display :: display_footer();
?>