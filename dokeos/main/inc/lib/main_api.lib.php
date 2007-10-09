<?php
// $Id$
/*
===============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Hugues Peeters
	Copyright (c) Christophe Gesche
	Copyright (c) Roan Embrechts (Vrije Universiteit Brussel)
	Copyright (c) Patrick Cool
	Copyright (c) Olivier Brouckaert
	Copyright (c) Toon Van Hoecke
	Copyright (c) Denes Nagy

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
===============================================================================
*/
/**
==============================================================================
*	This is a code library for Dokeos.
*	It is included by default in every Dokeos file
*	(through including the claro_init_global.inc.php)
*
*	@package dokeos.library
==============================================================================
*/
/*
==============================================================================
		CONSTANTS
==============================================================================
*/

//USER STATUS CONSTANTS
/** global status of a user: student */
define('STUDENT', 5);
/** global status of a user: course manager */
define('COURSEMANAGER', 1);

//COURSE VISIBILITY CONSTANTS
/** only visible for course admin */
define('COURSE_VISIBILITY_CLOSED', 0);
/** only visible for users registered in the course*/
define('COURSE_VISIBILITY_REGISTERED', 1);
/** open for all registered users on the platform */
define('COURSE_VISIBILITY_OPEN_PLATFORM', 2);
/** open for the whole world */
define('COURSE_VISIBILITY_OPEN_WORLD', 3);
/** modified (visibility specified through detailed roles-rights system ) */
define('COURSE_VISIBILITY_MODIFIED', 4);

define('SUBSCRIBE_ALLOWED', 1);
define('SUBSCRIBE_NOT_ALLOWED', 0);
define('UNSUBSCRIBE_ALLOWED', 1);
define('UNSUBSCRIBE_NOT_ALLOWED', 0);

//CONSTANTS FOR api_get_path FUNCTION
define('WEB_PATH', 'WEB_PATH');
define('SYS_PATH', 'SYS_PATH');
define('REL_PATH', 'REL_PATH');
define('WEB_COURSE_PATH', 'WEB_COURSE_PATH');
define('SYS_COURSE_PATH', 'SYS_COURSE_PATH');
define('REL_COURSE_PATH', 'REL_COURSE_PATH');
define('REL_CLARO_PATH', 'REL_CLARO_PATH');
define('WEB_CODE_PATH', 'WEB_CODE_PATH');
define('SYS_CODE_PATH', 'SYS_CODE_PATH');
define('SYS_LANG_PATH', 'SYS_LANG_PATH');
define('WEB_IMG_PATH', 'WEB_IMG_PATH');
define('GARBAGE_PATH', 'GARBAGE_PATH');
define('PLUGIN_PATH', 'PLUGIN_PATH');
define('WEB_PLUGIN_PATH', 'WEB_PLUGIN_PATH');
define('SYS_ARCHIVE_PATH', 'SYS_ARCHIVE_PATH');
define('INCLUDE_PATH', 'INCLUDE_PATH');
define('LIBRARY_PATH', 'LIBRARY_PATH');
define('CONFIGURATION_PATH', 'CONFIGURATION_PATH');

//CONSTANTS defining all tools, using the english version
define('TOOL_DOCUMENT', 'document');
define('TOOL_CALENDAR_EVENT', 'calendar_event');
define('TOOL_LINK', 'link');
define('TOOL_COURSE_DESCRIPTION', 'course_description');
define('TOOL_LEARNPATH', 'learnpath');
define('TOOL_ANNOUNCEMENT', 'announcement');
define('TOOL_BB_FORUM', 'bb_forum');
define('TOOL_BB_THREAD', 'bb_thread');
define('TOOL_BB_POST', 'bb_post');
define('TOOL_DROPBOX', 'dropbox');
define('TOOL_QUIZ', 'quiz');
define('TOOL_USER', 'user');
define('TOOL_GROUP', 'group');
define('TOOL_CHAT', 'chat');
define('TOOL_CONFERENCE', 'conference');
define('TOOL_STUDENTPUBLICATION', 'student_publication');
define('TOOL_TRACKING', 'tracking');
define('TOOL_HOMEPAGE_LINK', 'homepage_link');
define('TOOL_COURSE_SETTING', 'course_setting');
define('TOOL_BACKUP', 'backup');
define('TOOL_COPY_COURSE_CONTENT', 'copy_course_content');
define('TOOL_RECYCLE_COURSE', 'recycle_course');
define('TOOL_COURSE_HOMEPAGE', 'course_homepage');
define('TOOL_COURSE_RIGHTS_OVERVIEW', 'course_rights');

// CONSTANTS defining dokeos sections
define('SECTION_COURSES', 'mycourses');
define('SECTION_MYPROFILE', 'myprofile');
define('SECTION_MYAGENDA', 'myagenda');
define('SECTION_COURSE_ADMIN', 'course_admin');
define('SECTION_PLATFORM_ADMIN', 'platform_admin');

// CONSTANT name for local authentication source
define('PLATFORM_AUTH_SOURCE', 'platform');

/*
==============================================================================
		PROTECTION FUNCTIONS
		use these to protect your scripts
==============================================================================
*/
/**
* Function used to protect a course script.
* The function blocks access when there is no $_SESSION["_course"] defined.
* In all course scripts, this function is combined with
* RolesRights::protect_location($role_id, $location_id);
* to form a complete protection.
*
* @author Roan Embrechts
*/
function api_protect_course_script()
{
	if (!isset ($_SESSION["_course"]))
	{
		include (api_get_include_path()."/claro_init_header.inc.php");
		api_not_allowed();
	}
}

/**
* Function used to protect an admin script.
* The function blocks access when the user has no platform admin rights.
* This is only the first proposal, test and improve!
*
* @author Roan Embrechts
*/
function api_protect_admin_script()
{
	if (isset($_SESSION['_uid']))
	{
		require_once dirname(__FILE__).'/../../../users/lib/usermanager/usermanager.class.php';
		$usermgr = new UserManager($_SESSION['_uid']);
		if (!$usermgr->get_user()->is_platform_admin())
		{
			include (api_get_include_path()."/claro_init_header.inc.php");
			api_not_allowed();
		}
	}
	else
	{
		include (api_get_include_path()."/claro_init_header.inc.php");
		api_not_allowed();
	}
}

/**
* Function used to prevent anonymous users from accessing a script.
*
* @author Roan Embrechts
*/
function api_block_anonymous_users()
{
	$_uid = api_get_user_id();
	if (!(isset ($_uid) && $_uid))
	{
		include (api_get_include_path()."/claro_init_header.inc.php");
		api_not_allowed();
	}
}

/*
==============================================================================
		ACCESSOR FUNCTIONS
		don't access kernel variables directly,
		use these functions instead
==============================================================================
*/
/**
*	@return an array with the navigator name and version
*/
function api_get_navigator()
{
	$navigator = 'Unknown';
	$version = 0;
	if (strstr($_SERVER['HTTP_USER_AGENT'], 'Opera'))
	{
		$navigator = 'Opera';
		list (, $version) = explode('Opera', $_SERVER['HTTP_USER_AGENT']);
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
	{
		$navigator = 'Internet Explorer';
		list (, $version) = explode('MSIE', $_SERVER['HTTP_USER_AGENT']);
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], 'Gecko'))
	{
		$navigator = 'Mozilla';
		list (, $version) = explode('; rv:', $_SERVER['HTTP_USER_AGENT']);
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], 'Netscape'))
	{
		$navigator = 'Netscape';
		list (, $version) = explode('Netscape', $_SERVER['HTTP_USER_AGENT']);
	}
	$version = doubleval($version);
	if (!strstr($version, '.'))
	{
		$version = number_format(doubleval($version), 1);
	}
	return array ('name' => $navigator, 'version' => $version);
}
/**
*	@return True if user selfregistration is allowed, false otherwise.
*/
function api_is_self_registration_allowed()
{
	return $GLOBALS["allowSelfReg"];
}
/**
*	Returns a full path to a certain Dokeos area, which you specify
*	through a parameter.
*
*	See $coursesRepositoryAppend in the claro_main.conf.php
*	to alter the WEB_COURSE_PATH and SYS_COURSE_PATH parameters.
*
*	@param one of the following constants:
*	WEB_PATH, SYS_PATH, REL_PATH, WEB_COURSE_PATH, SYS_COURSE_PATH,
*	REL_COURSE_PATH, REL_CLARO_PATH, WEB_CODE_PATH, SYS_CODE_PATH,
*	SYS_LANG_PATH, WEB_IMG_PATH, GARBAGE_PATH, PLUGIN_PATH, SYS_ARCHIVE_PATH,
*	INCLUDE_PATH, LIBRARY_PATH, CONFIGURATION_PATH
*/
function api_get_path($path_type)
{
	switch ($path_type)
	{
		case WEB_PATH :
			return $GLOBALS['rootWeb'];
		case SYS_PATH :
			return $GLOBALS['rootSys'];
		case REL_PATH :
			return (substr($GLOBALS['urlAppend'], -1) === '/' ? $GLOBALS['urlAppend'] : $GLOBALS['urlAppend'].'/');
		case WEB_COURSE_PATH :
			return $GLOBALS['coursesRepositoryWeb'];
		case SYS_COURSE_PATH :
			return $GLOBALS['coursesRepositorySys'];
		case REL_COURSE_PATH :
			return api_get_path(REL_PATH).$GLOBALS['coursesRepositoryAppend'];
		case REL_CLARO_PATH :
			return api_get_path(REL_PATH).$GLOBALS['clarolineRepositoryAppend'];
		case WEB_CODE_PATH :
			return $GLOBALS['clarolineRepositoryWeb'];
		case SYS_CODE_PATH :
			return $GLOBALS['clarolineRepositorySys'];
		case SYS_LANG_PATH :
			return api_get_path(SYS_CODE_PATH).'lang/';
		case WEB_IMG_PATH :
			return api_get_path(WEB_CODE_PATH).'img/';
		case GARBAGE_PATH :
			return $GLOBALS['garbageRepositorySys'];
		case PLUGIN_PATH :
			return api_get_path(SYS_PATH).'plugin/';
		case WEB_PLUGIN_PATH :
			return api_get_path(WEB_PATH).'plugin/';
		case SYS_ARCHIVE_PATH :
			return api_get_path(SYS_PATH).'archive/';
		case INCLUDE_PATH :
			return str_replace('\\', '/', $GLOBALS['includePath']);
		case LIBRARY_PATH :
			return api_get_path(INCLUDE_PATH).'/lib';
		case CONFIGURATION_PATH :
			return api_get_path(INCLUDE_PATH).'/conf';
		default :
			return;
	}
}
/**
*	Alias for api_get_path(REL_PATH).
*	@return the relative web path for Dokeos (/...)
*/
function api_get_root_rel()
{
	return api_get_path(REL_PATH);
}
/**
*	Alias for api_get_path(SYS_CODE_PATH).
*	@return the filesystem code path e.g. /xxx/dokeos/main/
*/
function api_get_code_sys_path()
{
	return api_get_path(SYS_CODE_PATH);
}
/**
*	Alias for api_get_path(WEB_CODE_PATH).
*	@return the url code path e.g. http://xxx/dokeos/main/
*/
function api_get_code_web_path()
{
	return api_get_path(WEB_CODE_PATH);
}
/**
*	Alias for api_get_path(INCLUDE_PATH).
*	@return the path of the include (inc) directory
*/
function api_get_include_path()
{
	return api_get_path(INCLUDE_PATH);
}
/**
*	Alias for api_get_path(LIBRARY_PATH).
*	@return the path of the library (lib) directory
*/
function api_get_library_path()
{
	return api_get_path(LIBRARY_PATH);
}
/**
*	Alias for api_get_path(CONFIGURATION_PATH).
*	@return the path of the configuration (conf) directory
*/
function api_get_configuration_path()
{
	return api_get_path(CONFIGURATION_PATH);
}
/**
* note: this function was deprecated but turned out to be quite useful for
* API functions, so it will remain here.
*
* @return the id of the current user
*/
function api_get_user_id()
{
	return $_SESSION["_uid"];
}
/**
 * @param $user_id (integer): the id of the user
 * @return $user_info (array): user_id, lastname, firstname, username, email, ...
 * @author Patrick Cool <patrick.cool@UGent.be>
 * @version 21 September 2004
 * @desc find all the information about a user. If no paramater is passed you find all the information about the current user.
*/
function api_get_user_info($user_id = '')
{
	global $tbl_user;
	if ($user_id == '')
	{
		return $GLOBALS["_user"];
	}
	else
	{
		$sql = "SELECT * FROM ".Database :: get_main_table(MAIN_USER_TABLE)." WHERE user_id='".mysql_real_escape_string($user_id)."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		if(mysql_num_rows($result) > 0)
		{
			$result_array = mysql_fetch_array($result);
			// this is done so that it returns the same array-index-names
			// ideally the names of the fields of the user table are renamed so that they match $_user (or vice versa)
			// $_user should also contain every field of the user table (except password maybe). This would make the
			// following lines obsolete (and the code cleaner and slimmer !!!
			$user_info['firstName'] = $result_array['firstname'];
			$user_info['lastName'] = $result_array['lastname'];
			$user_info['mail'] = $result_array['email'];
			$user_info['picture_uri'] = $result_array['picture_uri'];
			$user_info['user_id'] = $result_array['user_id'];
			$user_info['official_code'] = $result_array['official_code'];
			$user_info['status'] = $result_array['status'];
			$user_info['language'] = $result_array['language'];
			$user_info['disk_quota'] = $result_array['disk_quota'];
			$user_info['database_quota'] = $result_array['database_quota'];
			$user_info['version_quota'] = array();
			$user_info['version_quota']['general'] = $result_array['version_quota'];

			$lot_sql = "SELECT * FROM ".Database :: get_main_table(MAIN_USER_QUOTA_TABLE)." WHERE user_id='".mysql_real_escape_string($user_id)."'";
			$lot_result = api_sql_query($lot_sql, __FILE__, __LINE__);
			if(mysql_num_rows($lot_result) > 0)
			{
				while($lot_result_array = mysql_fetch_array($lot_result))
				{
					$user_info['version_quota'][$lot_result_array['learning_object_type']] = $lot_result_array['user_quota'];
				}
			}

			return $user_info;
		}
		return false;
	}
}
/**
 * Returns the current course id (integer)
*/
function api_get_course_id()
{
	return $GLOBALS["_cid"];
}
/**
 * Returns the current course directory
 *
 * This function relies on api_get_course_info()
 * @return	string	The directory where the course is located inside the Dokeos "courses" directory
 * @author	Yannick Warnier <yannick.warnier@dokeos.com>
*/
function api_get_course_path()
{
	$info = api_get_course_info();
	return $info['path'];
}
/**
 * Returns the cidreq parameter name + current course id
*/
function api_get_cidreq()
{
	if (!empty ($GLOBALS["_cid"]))
	{
		return 'cidReq='.$GLOBALS["_cid"];
	}
	return '';
}
/**
*	Returns the current course info array.
*	Note: this array is only defined if the user is inside a course.
*	Array elements:
*	['name']
*	['official_code']
*	['sysCode']
*	['path']
*	['dbName']
*	['dbNameGlu']
*	['titular']
*	['language']
*	['extLink']['url' ]
*	['extLink']['name']
*	['categoryCode']
*	['categoryName']
*
* @todo	same behaviour as api_get_user_info so that api_get_course_id becomes absolete too
*/
function api_get_course_info()
{
	global $_course;
	return $_course;
}

/*
==============================================================================
		DATABASE QUERY MANAGEMENT
==============================================================================
*/
/**
 * Executes an SQL query
 * You have to use addslashes() on each value that you want to record into the database
 *
 * @author Olivier Brouckaert
 * @param  string $query - SQL query
 * @param  string $file - optional, the file path and name of the error (__FILE__)
 * @param  string $line - optional, the line of the error (__LINE__)
 * @return resource - the return value of the query
 */
function api_sql_query($query, $file = '', $line = 0)
{
	$result = mysql_query($query);
	if ($line && !$result)
	{
		if (api_get_setting('server_type') !== 'test')
		{
			@ mysql_close();
			die('SQL error in file <b>'.$file.'</b> at line <b>'.$line.'</b>');
		}
		else
		{
			$info = '<pre>';
			$info .= '<b>MYSQL ERROR :</b><br/> ';
			$info .= mysql_error();
			$info .= '<br/>';
			$info .= '<b>QUERY       :</b><br/> ';
			$info .= $query;
			$info .= '<br/>';
			$info .= '<b>FILE        :</b><br/> ';
			$info .= ($file == '' ? ' unknown ' : $file);
			$info .= '<br/>';
			$info .= '<b>LINE        :</b><br/> ';
			$info .= ($line == 0 ? ' unknown ' : $line);
			$info .= '</pre>';
			@ mysql_close();
			die($info);
		}
	}
	return $result;
}
/**
 * Store the result of a query into an array
 *
 * @author Olivier Brouckaert
 * @param  resource $result - the return value of the query
 * @return array - the value returned by the query
 */
function api_store_result($result)
{
	$tab = array ();
	while ($row = mysql_fetch_array($result))
	{
		$tab[] = $row;
	}
	return $tab;
}

/*
==============================================================================
		SESSION MANAGEMENT
==============================================================================
*/
/**
 * start the dokeos session
 *
 * @author Olivier Brouckaert
 * @param  string variable - the variable name to save into the session
 */
function api_session_start($already_installed = true)
{
	global $storeSessionInDb;
	if (is_null($storeSessionInDb))
	{
		$storeSessionInDb = false;
	}
	if ($storeSessionInDb && function_exists('session_set_save_handler'))
	{
		include_once (api_get_library_path().'/session_handler.class.php');
		$session_handler = new session_handler();
		@ session_set_save_handler(array (& $session_handler, 'open'), array (& $session_handler, 'close'), array (& $session_handler, 'read'), array (& $session_handler, 'write'), array (& $session_handler, 'destroy'), array (& $session_handler, 'garbage'));
	}
	session_name('dk_sid');
	session_start();
	if ($already_installed)
	{
		if (empty ($_SESSION['checkDokeosURL']))
		{
			$_SESSION['checkDokeosURL'] = api_get_path(WEB_PATH);
		}
		elseif ($_SESSION['checkDokeosURL'] != api_get_path(WEB_PATH))
		{
			api_session_clear();
		}
	}
}
/**
 * save a variable into the session
 *
 * BUG: function works only with global variables
 *
 * @author Olivier Brouckaert
 * @param  string variable - the variable name to save into the session
 */
function api_session_register($variable)
{
	global $$variable;
	session_register($variable);
	$_SESSION[$variable] = $$variable;
}
/**
 * Remove a variable from the session.
 *
 * @author Olivier Brouckaert
 * @param  string variable - the variable name to remove from the session
 */
function api_session_unregister($variable)
{
	session_unregister($variable);
	$_SESSION[$variable] = null;
	unset ($GLOBALS[$variable]);
}
/**
 * Clear the session
 *
 * @author Olivier Brouckaert
 */
function api_session_clear()
{
	session_regenerate_id();
	session_unset();
	$_SESSION = array ();
}
/**
 * Destroy the session
 *
 * @author Olivier Brouckaert
 */
function api_session_destroy()
{
	session_unset();
	$_SESSION = array ();
	session_destroy();
}

/*
==============================================================================
		STRING MANAGEMENT
==============================================================================
*/
function api_add_url_param($url, $param)
{
	if (empty ($param))
	{
		return $url;
	}
	if (strstr($url, '?'))
	{
		if ($param[0] != '&')
		{
			$param = '&'.$param;
		}
		list (, $query_string) = explode('?', $url);
		$param_list1 = explode('&', $param);
		$param_list2 = explode('&', $query_string);
		$param_list1_keys = $param_list1_vals = array ();
		foreach ($param_list1 as $key => $enreg)
		{
			list ($param_list1_keys[$key], $param_list1_vals[$key]) = explode('=', $enreg);
		}
		$param_list1 = array ('keys' => $param_list1_keys, 'vals' => $param_list1_vals);
		foreach ($param_list2 as $enreg)
		{
			$enreg = explode('=', $enreg);
			$key = array_search($enreg[0], $param_list1['keys']);
			if (!is_null($key) && !is_bool($key))
			{
				$url = str_replace($enreg[0].'='.$enreg[1], $enreg[0].'='.$param_list1['vals'][$key], $url);
				$param = str_replace('&'.$enreg[0].'='.$param_list1['vals'][$key], '', $param);
			}
		}
		$url .= $param;
	}
	else
	{
		$url = $url.'?'.$param;
	}
	return $url;
}
/**
* Returns a difficult to guess password.
* @param int $length, the length of the password
* @return string the generated password
*/
function api_generate_password($length = 8)
{
	$characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
	if ($length < 2)
	{
		$length = 2;
	}
	$password = '';
	for ($i = 0; $i < $length; $i ++)
	{
		$password .= $characters[rand() % strlen($characters)];
	}
	return $password;
}
/**
* Checks a password to see wether it is OK to use.
* @param string $password
* @return true if the password is acceptable, false otherwise
*/
function api_check_password($password)
{
	$lengthPass = strlen($password);
	if ($lengthPass < 5)
	{
		return false;
	}
	$passLower = strtolower($password);
	$cptLettres = $cptChiffres = 0;
	for ($i = 0; $i < $lengthPass; $i ++)
	{
		$codeCharCur = ord($passLower[$i]);
		if ($i && abs($codeCharCur - $codeCharPrev) <= 1)
		{
			$consecutif ++;
			if ($consecutif == 3)
			{
				return false;
			}
		}
		else
		{
			$consecutif = 1;
		}
		if ($codeCharCur >= 97 && $codeCharCur <= 122)
		{
			$cptLettres ++;
		}
		elseif ($codeCharCur >= 48 && $codeCharCur <= 57)
		{
			$cptChiffres ++;
		}
		else
		{
			return false;
		}
		$codeCharPrev = $codeCharCur;
	}
	return ($cptLettres >= 3 && $cptChiffres >= 2) ? true : false;
}
/**
 * truncates a string
 *
 * @author Brouckaert Olivier
 * @param  string text - text to truncate
 * @param  integer length - length of the truncated text
 * @param  string endStr - suffix
 * @param  boolean middle - if true, truncates on string middle
 */
function api_trunc_str($text, $length = 30, $endStr = '...', $middle = false)
{
	if (strlen($text) <= $length)
	{
		return $text;
	}
	if ($middle)
	{
		$text = rtrim(substr($text, 0, round($length / 2))).$endStr.ltrim(substr($text, -round($length / 2)));
	}
	else
	{
		$text = rtrim(substr($text, 0, $length)).$endStr;
	}
	return $text;
}
// deprecated, use api_trunc_str() instead
function shorten($input, $length = 15)
{
	$length = intval($length);
	if (!$length)
	{
		$length = 15;
	}
	return api_trunc_str($input, $length);
}
/**
 * handling simple and double apostrofe in order that strings be stored properly in database
 *
 * @author Denes Nagy
 * @param  string variable - the variable to be revised
 */
function domesticate($input)
{
	$input = stripslashes($input);
	$input = str_replace("'", "''", $input);
	$input = str_replace('"', "''", $input);
	return ($input);
}

/*
==============================================================================
		FAILURE MANAGEMENT
==============================================================================
*/

/*
 * The Failure Management module is here to compensate
 * the absence of an 'exception' device in PHP 4.
 */
/**
 * $api_failureList - array containing all the failure recorded
 * in order of arrival.
 */
$api_failureList = array ();
/**
 * Fills a global array called $api_failureList
 * This array collects all the failure occuring during the script runs
 * The main purpose is allowing to manage the display messages externaly
 * from the functions or objects. This strengthens encupsalation principle
 *
 * @author Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @param  string $failureType - the type of failure
 * @global array $api_failureList
 * @return bolean false to stay consistent with the main script
 */
function api_set_failure($failureType)
{
	global $api_failureList;
	$api_failureList[] = $failureType;
	return false;
}
/**
 * get the last failure stored in $api_failureList;
 *
 * @author Hugues Peeters <hugues.peeters@claroline.net>
 * @param void
 * @return string - the last failure stored
 */
function api_get_last_failure()
{
	global $api_failureList;
	return $api_failureList[count($api_failureList) - 1];
}
/**
 * collects and manage failures occuring during script execution
 * The main purpose is allowing to manage the display messages externaly
 * from functions or objects. This strengthens encupsalation principle
 *
 * @author Hugues Peeters <hugues.peeters@claroline.net>
 * @package dokeos.library
 */
class api_failure
{
	/*
	 * IMPLEMENTATION NOTE : For now the $api_failureList list is set to the
	 * global scope, as PHP 4 is unable to manage static variable in class. But
	 * this feature is awaited in PHP 5. The class is already written to minize
	 * the change when static class variable will be possible. And the API won't
	 * change.
	 */
	var $api_failureList = array ();
	/**
	 * Pile the last failure in the failure list
	 *
	 * @author Hugues Peeters <peeters@ipm.ucl.ac.be>
	 * @param  string $failureType - the type of failure
	 * @global array  $api_failureList
	 * @return bolean false to stay consistent with the main script
	 */
	function set_failure($failureType)
	{
		global $api_failureList;
		$api_failureList[] = $failureType;
		return false;
	}
	/**
	 * get the last failure stored
	 *
	 * @author Hugues Peeters <hugues.peeters@claroline.net>
	 * @param void
	 * @return string - the last failure stored
	 */
	function get_last_failure()
	{
		global $api_failureList;
		return $api_failureList[count($api_failureList) - 1];
	}
}

/*
==============================================================================
		CONFIGURATION SETTINGS
==============================================================================
*/
/**
* DEPRECATED, use api_get_setting instead
*/
function get_setting($variable, $key = NULL)
{
	global $_setting;
	return is_null($key) ? $_setting[$variable] : $_setting[$variable][$key];
}

/**
* Returns the value of a setting from the web-adjustable admin config settings.
*
* WARNING true/false are stored as string, so when comparing you need to check e.g.
* if(api_get_setting("show_navigation_menu") == "true") //CORRECT
* instead of
* if(api_get_setting("show_navigation_menu") == true) //INCORRECT
*
* @author Rene Haentjens
* @author Bart Mollet
*/
function api_get_setting($variable, $key = NULL)
{
	global $_setting;
	return is_null($key) ? $_setting[$variable] : $_setting[$variable][$key];
}

/*
==============================================================================
		LANGUAGE SUPPORT
==============================================================================
*/

/**
 * Gets a localized translation for the given string identifier.
 * @param string $variable the string identifier.
 * @return string the translation.
 * @author Evie Embrechts
 * @author Patrick Cool
 * @author Tim De Pauw
 */
function get_lang($variable)
{
	global $language_interface, $_language_files;
	// TODO: convert files to a new, more convenient format in advance
	foreach ($_language_files as $language_file => &$val)
	{
		if (!is_array($val))
		{
			$filename = api_get_path(SYS_CODE_PATH).'lang'.DIRECTORY_SEPARATOR.$language_interface.DIRECTORY_SEPARATOR.$language_file.'.inc.php';
			$val = array();
			if (is_file($filename))
			{
				foreach (file($filename) as $line)
				{
					if (preg_match('/^\s*\$(?:lang)?(\S+)\s*=\s*((["\']).*?\3)\s*;\s*$/', rtrim($line), $matches))
					{
						$name = $matches[1];
						$value = eval('return ' . $matches[2] . ';');
						$val[$name] = $value;
					}
				}
			}
		}
		if (isset($val[$variable]))
		{
			return $val[$variable];
		}
	}
	return '[=' . $variable . '=]';
}

/**
 * Adds the given identifiers to the collection of language files to use.
 * Previously, this was done by accessing $langFile.
 */
function api_use_lang_files()
{
	global $_language_files;
	foreach (func_get_args() as $id)
	{
		if (is_array($id))
		{
			foreach ($id as $i)
			{
				$_language_files[$i] = true;
			}
		}
		else
		{
			$_language_files[$id] = true;
		}
	}
}

/**
 * Removes the given identifiers from the collection of language files to use.
 */
function api_ignore_lang_files()
{
	global $_language_files;
	foreach (func_get_args() as $id)
	{
		if (is_array($id))
		{
			foreach ($id as $i)
			{
				unset($_language_files[$i]);
			}
		}
		else
		{
			unset($_language_files[$id]);
		}
	}
}

/*
==============================================================================
		USER PERMISSIONS
==============================================================================
*/
/**
* Check if current user is a platform administrator
* @return true if the user has platform admin rights,
* false otherwise.
*/
function api_is_platform_admin()
{
	return $_SESSION["is_platformAdmin"];
}
/**
 * Check if current user is allowed to create courses
* @return true if the user has course creation rights,
* false otherwise.
*/
function api_is_allowed_to_create_course()
{
	return $_SESSION["is_allowedCreateCourse"];
}
/**
 * Check if the current user is a course administrator
 * @return boolean True if current user is a course administrator
 */
function api_is_course_admin()
{
	return $_SESSION["is_courseAdmin"];
}

/*
==============================================================================
		DISPLAY OPTIONS
		student view, title, message boxes,...
==============================================================================
*/
/**
 * Displays the title of a tool.
 * Normal use: parameter is a string:
 * api_display_tool_title("My Tool")
 *
 * Optionally, there can be a subtitle below
 * the normal title, and / or a supra title above the normal title.
 *
 * e.g. supra title:
 * group
 * GROUP PROPERTIES
 *
 * e.g. subtitle:
 * AGENDA
 * calender & events tool
 *
 * @author Hugues Peeters <hugues.peeters@claroline.net>
 * @param  mixed $titleElement - it could either be a string or an array
 *                               containing 'supraTitle', 'mainTitle',
 *                               'subTitle'
 * @return void
 */
function api_display_tool_title($titleElement)
{
	if (is_string($titleElement))
	{
		$tit = $titleElement;
		unset ($titleElement);
		$titleElement['mainTitle'] = $tit;
	}
	echo '<h3>';
	if ($titleElement['supraTitle'])
	{
		echo '<small>'.$titleElement['supraTitle'].'</small><br>';
	}
	if ($titleElement['mainTitle'])
	{
		echo $titleElement['mainTitle'];
	}
	if ($titleElement['subTitle'])
	{
		echo '<br><small>'.$titleElement['subTitle'].'</small>';
	}
	echo '</h3>';
}

/**
* Display the different ways (roles) to view a course tool.
* Course admins can see the course through the eyes of other roles
* so they can experience how the course looks like for e.g. a student,
* a teaching assistant, a guest...
* @todo move javascript to a decent location
*/
function api_display_tool_view_option()
{
	//list of all local roles
	$local_role_list = RolesRights :: get_local_role_list();
	//determine local role of current user
	$user_id = api_get_user_id();
	$course_id = api_get_course_id();
	$role_id = RolesRights :: get_local_user_role_id($user_id, $course_id);

	$view_as_role = $_SESSION['view_as_role'];
	if (isset ($view_as_role) && $view_as_role)
		$role_id = $view_as_role;

	//some extra work needed:
	//url must keep the existing GET parameters
	if (strpos($_SERVER['REQUEST_URI'], '?') === false)
	{
		$url = $_SERVER['PHP_SELF'].'?';
	}
	else
	{
		$url = $_SERVER['REQUEST_URI'];
	}

	//remove old view_as_role get parameters if there are any
	//$url = preg_replace('/\?view_as_role=\d/', '', $url);
	$url = preg_replace('/\&view_as_role=\d/', '', $url);
?>
	<script type="text/javascript">
	/* <![CDATA[ */
	function viewRoleMenu(targ,selObj,restore)
	{
	  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	  if (restore) selObj.selectedIndex=0;
	}
	/* ]]> */
	</script>
	<?php


	//display roles dropdown menu
	//by default the current role (view as role, or real role if no other view) is selected
	echo get_lang('ViewAsRole').'&nbsp;';
	echo '<form class="view_role_form" id="view_role_form" name="view_role_form" method="post" action="'.$_SERVER['PHP_SELF'].'">', "\n\t<select name=\"role_list\" onchange=\"viewRoleMenu('parent',this,0)\">\n";
	foreach ($local_role_list as $local_role)
	{
		$selected = ($local_role['id'] == $role_id) ? ' selected="selected"' : '';
		echo "\t\t<option value=\"".$url."&amp;view_as_role=".$local_role['id']."\"$selected>".get_lang($local_role['name'])."</option>\n";
	}
	echo "\t</select>\n\t";
	echo '<noscript><input type="submit" name="select_role_view" value="'.get_lang('Ok').'" /></noscript>';
	echo "\n</form>\n";
}

/**
 * Displays the contents of an array in a messagebox.
 * @param array $info_array An array with the messages to show
 */
function api_display_array($info_array)
{
	foreach ($info_array as $element)
	{
		$message .= $element."<br>";
	}
	Display :: display_normal_message($message);
}
/**
*	Displays debug info
* @param string $debug_info The message to display
*	@author Roan Embrechts
*	@version 1.1, March 2004
*/
function api_display_debug_info($debug_info)
{
	$message = "<i>Debug info</i><br>";
	$message .= $debug_info;
	Display :: display_normal_message($message);
}
/**
*	@deprecated, use api_is_allowed_to_edit() instead
*/
function is_allowed_to_edit()
{
	return api_is_allowed_to_edit();
}
/**
*	Function that removes the need to directly use is_courseAdmin global in
*	tool scripts. It returns true or false depending on the user's rights in
*	this particular course.
*
*	@author Roan Embrechts
*	@author Patrick Cool
*	@version 1.1, February 2004
*	@return boolean, true: the user has the rights to edit, false: he does not
*/
function api_is_allowed_to_edit()
{
	$is_courseAdmin = api_is_course_admin();
	if (is_student_view_enabled())
	{
		$is_allowed = $is_courseAdmin && $_SESSION["studentview"] != "studentenview";
		return $is_allowed;
	}
	else
		return $is_courseAdmin;
}
/**
*	Displays message "You are not allowed here..."
*	and exits the entire script.
*
*	@author Roan Embrechts
*	@version 1.0, February 2004
*/
function api_not_allowed()
{
	$home_url = api_get_path(WEB_PATH);
	Display :: display_error_message("<p>Either you are not allowed here or your session has expired.<br><br>You may try <a href=\"$home_url\" target=\"_top\">reconnecting on the home page</a>.</p>");
	$_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
	exit;
}
/**
* Returns true if student view option is enabled, false otherwise. If it is
* true, tools can provide a student / course manager switch option. (see
* display_tool_view_option() )
* @return boolean True if student view option is enabled.
*/
function is_student_view_enabled()
{
	return api_get_setting('show_student_view') == 'true';
}

/*
==============================================================================
		WHAT'S NEW
		functions for the what's new icons
		in the user course list
==============================================================================
*/
/**
 * @param $last_post_datetime standard output date in a sql query
 * @return unix timestamp
 * @author Toon Van Hoecke <Toon.VanHoecke@UGent.be>
 * @version October 2003
 * @desc convert sql date to unix timestamp
*/
function convert_mysql_date($last_post_datetime)
{
	list ($last_post_date, $last_post_time) = split(" ", $last_post_datetime);
	list ($year, $month, $day) = explode("-", $last_post_date);
	list ($hour, $min, $sec) = explode(":", $last_post_time);
	$announceDate = mktime($hour, $min, $sec, $month, $day, $year);
	return $announceDate;
}

/**
 * Updates or adds item properties to the Item_propetry table
 * Tool and lastedit_type are language independant strings (langvars->get_lang!)
 *
 * @param array $_course
 * @param $_course : array with course properties
 * @param $tool : tool id, linked to 'rubrique' of the course tool_list (Warning: language sensitive !!)
 * @param $item_id : id of the item itself, linked to key of every tool ('id', ...), "*" = all items of the tool
 * @param $lastedit_type : add or update action (1) message to be translated (in trad4all) : e.g. DocumentAdded, DocumentUpdated;
 * 												(2) "delete"; (3) "visible"; (4) "invisible";
 * @param $user_id : id of the editing/adding user
 * @param $to_group_id : id of the intended group ( 0 = for everybody), only relevant for $type (1)
 * @param $to_user_id : id of the intended user (always has priority over $to_group_id !), only relevant for $type (1)
 * @param string $start_visible 0000-00-00 00:00:00 format
 * @param unknown_type $end_visible 0000-00-00 00:00:00 format
 * @return false if fails
 * @author Toon Van Hoecke <Toon.VanHoecke@UGent.be>, Ghent University
 * @version January 2005
 * @desc update the item_properties table (if entry not exists, insert) of the course
 */
function api_item_property_update($_course, $tool, $item_id, $lastedit_type, $user_id, $to_group_id = 0, $to_user_id = NULL, $start_visible = 0, $end_visible = 0)
{
	$time = time();
	$time = date("Y-m-d H:i:s", $time);
	$TABLE_ITEMPROPERTY = Database :: get_course_table(ITEM_PROPERTY_TABLE);
	if ($to_user_id <= 0)
		$to_user_id = NULL; //no to_user_id set
	$start_visible = ($start_visible == 0) ? "0000-00-00 00:00:00" : $start_visible;
	$end_visible = ($end_visible == 0) ? "0000-00-00 00:00:00" : $end_visible;
	// set filters for $to_user_id and $to_group_id, with priority for $to_user_id
	$filter = "tool='$tool' AND ref='$item_id'";
	if ($item_id == "*")
		$filter = "tool='$tool' AND visibility<>'2'"; // for all (not deleted) items of the tool
	// check if $to_user_id and $to_group_id are passed in the function call
	// if both are not passed (both are null) then it is a message for everybody and $to_group_id should be 0 !
	if (is_null($to_user_id) && is_null($to_group_id))
		$to_group_id = 0;
	if (!is_null($to_user_id))
		$to_filter = " AND to_user_id='$to_user_id'"; // set filter to intended user
	else
		if (!is_null($to_group_id))
			$to_filter = " AND to_group_id='$to_group_id'"; // set filter to intended group
	// update if possible
	$set_type = "";
	switch ($lastedit_type)
	{
		case "delete" : // delete = make item only visible for the platform admin
			$visibility = '2';
			$sql = "UPDATE $TABLE_ITEMPROPERTY
										SET lastedit_date='$time', lastedit_user_id='$user_id', visibility='$visibility' $set_type
										WHERE $filter";
			break;
		case "visible" : // change item to visible
			$visibility = '1';
			$sql = "UPDATE $TABLE_ITEMPROPERTY
										SET lastedit_date='$time', lastedit_user_id='$user_id', visibility='$visibility' $set_type
										WHERE $filter";
			break;
		case "invisible" : // change item to invisible
			$visibility = '0';
			$sql = "UPDATE $TABLE_ITEMPROPERTY
										SET lastedit_date='$time', lastedit_user_id='$user_id', visibility='$visibility' $set_type
										WHERE $filter";
			break;
		default : // item will be added or updated
			$set_type = ", lastedit_type='$lastedit_type' ";
			$visibility = '1';
			$filter .= $to_filter;
			$sql = "UPDATE $TABLE_ITEMPROPERTY
										SET lastedit_date='$time', lastedit_user_id='$user_id' $set_type
										WHERE $filter";
	}

	$res = mysql_query($sql);
	// insert if no entries are found (can only happen in case of $lastedit_type switch is 'default')
	if (mysql_affected_rows() == 0)
	{
		if (!is_null($to_user_id)) // $to_user_id has more priority than $to_group_id
		{
			$to_field = "to_user_id";
			$to_value = $to_user_id;
		}
		else // $to_user_id is not set
			{
			$to_field = "to_group_id";
			$to_value = $to_group_id;
		}
		$sql = "INSERT INTO $TABLE_ITEMPROPERTY
						   		  			(tool,   ref,       insert_date,insert_user_id,lastedit_date,lastedit_type,   lastedit_user_id,$to_field,  visibility,   start_visible,   end_visible)
						         	VALUES 	('$tool','$item_id','$time',    '$user_id',	   '$time',		 '$lastedit_type','$user_id',	   '$to_value','$visibility','$start_visible','$end_visible')";
		$res = mysql_query($sql);
		if (!$res)
			return FALSE;
	}
	return TRUE;
}

/*
==============================================================================
		Language Dropdown
==============================================================================
*/
/**
*	Displays a form (drop down menu) so the user can select his/her preferred language.
*	The form works with or without javascript
*/
function api_display_language_form()
{
	$platformLanguage = api_get_setting('platformLanguage');
	$dirname = api_get_path(SYS_PATH)."main/lang/"; // this line is probably no longer needed
	// retrieve a complete list of all the languages.
	$language_list = api_get_languages();
	// the the current language of the user so that his/her language occurs as selected in the dropdown menu
	$user_selected_language = $_SESSION["user_language_choice"];
	if (!isset ($user_selected_language))
		$user_selected_language = $platformLanguage;
	$original_languages = $language_list['name'];
	$folder = $language_list['folder']; // this line is probably no longer needed
?>
	<script type="text/javascript">
	/* <![CDATA[ */
	function jumpMenu(targ,selObj,restore){ //v3.0
	  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	  if (restore) selObj.selectedIndex=0;
	}
	/* ]]> */
	</script>
	<?php


	echo "<form id=\"lang_form\" name=\"lang_form\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">", "<select name=\"language_list\"  onchange=\"jumpMenu('parent',this,0)\">";
	foreach ($original_languages as $key => $value)
	{
		if ($folder[$key] == $user_selected_language)
			$option_end = " selected=\"selected\" >";
		else
			$option_end = ">";
		echo "<option value=\"".$_SERVER['PHP_SELF']."?language=".$folder[$key]."\"$option_end";
		#echo substr($value,0,16); #cut string to keep 800x600 aspect
		echo $value;
		echo "</option>\n";
	}
	echo "</select>";
	echo "<noscript><input type=\"submit\" name=\"user_select_language\" value=\"".get_lang("Ok")."\" /></noscript>";
	echo "</form>";
}
/**
* Return a list of all the languages that are made available by the admin.
* @return array An array with all languages. Structure of the array is
*  array['name'] = An array with the name of every language
*  array['folder'] = An array with the corresponding dokeos-folder
*/
function api_get_languages()
{
	$tbl_language = Database :: get_main_table(MAIN_LANGUAGE_TABLE);
	;
	$sql = "SELECT * FROM $tbl_language WHERE available='1' ORDER BY original_name ASC";
	$result = api_sql_query($sql, __FILE__, __LINE__);
	while ($row = mysql_fetch_array($result))
	{
		$language_list['name'][] = $row['original_name'];
		$language_list['folder'][] = $row['dokeos_folder'];
	}
	return $language_list;
}

/*
==============================================================================
		HTML AREA
		functions for the WYSIWYG html editor, TeX parsing...
==============================================================================
*/
/**
* Displays the HtmlArea WYSIWYG editor for online editing of html
* @param string $name The name of the form-element
* @param string $content The default content of the html-editor
* @param int $height The height of the form element
* @param int $width The width of the form element
* @param string $optAttrib optional attributes for the form element
*/
function api_disp_html_area($name, $content = '', $height = '', $width = '100%', $optAttrib = '')
{
	global $urlAppend, $_course;
?>
<textarea id="<?php echo $name; ?>" name="<?php echo $name; ?>" rows="15" cols="70" style="width:<?php echo $width; ?>; <?php if(!empty($height)) echo "height:$height;"; ?>" <?php echo $optAttrib; ?> ><?php echo $content; ?></textarea>
<?php
}
/**
 * Apply parsing to content to parse tex commandos that are seperated by [tex]
 * [/tex] to make it readable for techexplorer plugin.
 * @param string $text The text to parse
 * @return string The text after parsing.
 * @author Patrick Cool <patrick.cool@UGent.be>
 * @version June 2004
*/
function api_parse_tex($textext)
{
	if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
	{
		$textext = str_replace(array ("[tex]", "[/tex]"), array ("<object classid=\"clsid:5AFAB315-AD87-11D3-98BB-002035EFB1A4\"><param name=\"autosize\" value=\"true\" /><param name=\"DataType\" value=\"0\" /><param name=\"Data\" value=\"", "\" /></object>"), $textext);
	}
	else
	{
		$textext = str_replace(array ("[tex]", "[/tex]"), array ("<embed type=\"application/x-techexplorer\" texdata=\"", "\" autosize=\"true\" pluginspage=\"http://www.integretechpub.com/techexplorer/\">"), $textext);
	}
	return $textext;
}

/**
 * Send an email.
 *
 * Wrapper function for the standard php mail() function. Change this function
 * to your needs. The parameters must follow the same rules as the standard php
 * mail() function. Please look at the documentation on http: //www. php.
 * net/manual/en/function. mail.php
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param string $additional_headers
 * @param string $additional_parameters
 */
function api_send_mail($to, $subject, $message, $additional_headers = null, $additional_parameters = null)
{
	return mail($to, $subject, $message, $additional_headers, $additional_parameters);
}

/**
 * Find the largest sort value in a given user_course_category
 * This function is used when we are moving a course to a different category
 * and also when a user subscribes to a courses (the new courses is added to the end
 * of the main category
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param int $user_course_category: the id of the user_course_category
 * @return int the value of the highest sort of the user_course_category
*/
function api_max_sort_value($user_course_category, $user_id)
{

	$tbl_course_user = Database :: get_main_table(MAIN_COURSE_USER_TABLE);

	$sql_max = "SELECT max(sort) as max_sort FROM $tbl_course_user WHERE user_id='".$user_id."' AND user_course_cat='".$user_course_category."'";
	$result_max = mysql_query($sql_max) or die(mysql_error());
	if (mysql_num_rows($result_max) == 1)
	{
		$row_max = mysql_fetch_array($result_max);
		$max_sort = $row_max['max_sort'];
	}
	else
	{
		$max_sort = 0;
	}

	return $max_sort;
}

/**
 * This function converts the string "true" or "false" to a boolean true or false.
 * This function is in the first place written for the Dokeos Config Settings (also named AWACS)
 * @param string "true" or "false"
 * @return boolean true or false
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 */
function string_2_boolean($string)
{
	if ($string == "true")
	{
		return true;
	}
	if ($string == "false")
	{
		return false;
	}
}

/**
* Show the so-called "left" menu for navigating
*/
function api_show_course_navigation_menu($isHidden = false)
{
	global $output_string_menu;
	global $_setting;

	// check if the $_SERVER['REQUEST_URI'] contains already url parameters (thus a questionmark)
	if (!strstr($_SERVER['REQUEST_URI'], "?"))
	{
		$sourceurl = $_SERVER['PHP_SELF']."?";
	}
	else
	{
		$sourceurl = $_SERVER['REQUEST_URI'];
	}
	$output_string_menu = "";
	if ($isHidden == "true" and $_SESSION["hideMenu"])
	{

		$_SESSION["hideMenu"] = "hidden";

		$sourceurl = str_replace("&isHidden=true", "", $sourceurl);
		$sourceurl = str_replace("&isHidden=false", "", $sourceurl);

		$output_string_menu .= " <a href='".$sourceurl."&isHidden=false'>"."<img src=../../main/img/expand.gif alt='Show menu1' padding:'2px'/>"."</a>";
	}
	elseif ($isHidden == "false" and $_SESSION["hideMenu"])
	{
		$sourceurl = str_replace("&isHidden=true", "", $sourceurl);
		$sourceurl = str_replace("&isHidden=false", "", $sourceurl);

		$_SESSION["hideMenu"] = "shown";
		$output_string_menu .= "<div id='leftimg'><a href='".$sourceurl."&isHidden=true'>"."<img src=../../main/img/collapse.gif alt='Hide menu2' padding:'2px'/>"."</a></div>";
	}
	elseif ($_SESSION["hideMenu"])
	{
		if ($_SESSION["hideMenu"] == "shown")
		{
			$output_string_menu .= "<div id='leftimg'><a href='".$sourceurl."&isHidden=true'>"."<img src='../../main/img/collapse.gif' alt='Hide menu3' padding:'2px'/>"."</a></div>";
		}
		if ($_SESSION["hideMenu"] == "hidden")
		{
			$sourceurl = str_replace("&isHidden=true", "", $sourceurl);
			$output_string_menu .= "<a href='".$sourceurl."&isHidden=false'>"."<img src='../../main/img/expand.gif' alt='Hide menu4' padding:'2px'/>"."</a>";

		}
	}
	elseif (!$_SESSION["hideMenu"])
	{
		$_SESSION["hideMenu"] = "shown";
		if (isset ($_cid))
		{
			$output_string_menu .= "<div id='leftimg'><a href='".$sourceurl."&isHidden=true'>"."<img src='main/img/collapse.gif' alt='Hide menu5' padding:'2px'/>"."</a></div>";
		}
	}
}

/**
 * Loads the applications installed on the system. Applications are classes
 * in the /application/lib subdirectory. Each application is a directory,
 * which in its turn contains a class file named after the application. For
 * instance, the weblcms application is the class Weblcms, defined in
 * /application/lib/weblcms/weblcms.class.php. Applications must extend the
 * Application class.
 */
function load_applications()
{
	$applications = array();
	$path = api_get_path(SYS_CODE_PATH).'../application/lib';
	if ($handle = opendir($path))
	{
		while (false !== ($file = readdir($handle)))
		{
			$toolPath = $path.'/'. $file .'/'.$file.'_manager';
			if (is_dir($toolPath) && is_application_name($file))
			{
				require_once $toolPath.'/'.$file.'.class.php';
				if (!in_array($file, applications))
				{
					$applications[] = $file;

				}
			}
		}
		closedir($handle);
	}
	return $applications;
}

function is_application_name($name)
{
	return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
}

function application_to_class($application)
{
	return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $application));
}
?>