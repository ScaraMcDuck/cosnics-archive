<?php // $Id$
/**
==============================================================================
* It is recommended that ALL dokeos scripts include this important file.
* This script manages
* - http get, post, post_files, session, server-vars extraction into global namespace;
*   (which doesn't occur anymore when servertype config setting is set to test,
*    and which will disappear completely in Dokeos 1.6.1)
* - selecting the main database;
* - include of language files.
*
* @package dokeos.include
==============================================================================
*/

// Determine the directory path where this current file lies
// This path will be useful to include the other intialisation files

$includePath = dirname(__FILE__);

// include the main Dokeos platform configuration file
$main_configuration_file_path = $includePath . '/configuration/configuration.php';
$already_installed = false;
if (file_exists($main_configuration_file_path))
{
	require_once($main_configuration_file_path);
	$already_installed = true;
}

$error_message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>Dokeos not installed!</title>
		<link rel="stylesheet" href="layout/aqua/css/common.css" type="text/css"/>
	</head>
	<body>
		<div id="header1">Dokeos not installed!</div>
		<div style="text-align: center;"><br /><br />
				<form action="install/index.php" method="get"><input type="submit" value="&nbsp;&nbsp; Click to INSTALL DOKEOS &nbsp;&nbsp;" /></form><br />
				or <a href="documentation/installation_guide.html" target="_blank">read the installation guide</a><br /><br />
		</div>
		<div id="footer">
			<div class="copyright">Platform <a href="http://www.dokeos.com"> Dokeos </a> &copy; ' . date('Y') . '</div>
			&nbsp;
		</div>
	</body>
</html>
';

// 
if (!$already_installed)
{
	die($error_message);
}

// include the main Dokeos platform library file

// TODO: Temporary solution till these are relocated to a more suitable location
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

// Add the path to the pear packages to the include path
require_once dirname(__FILE__).'/configuration/configuration.class.php';
require_once dirname(__FILE__).'/filesystem/path.class.php';
require_once Path :: get_library_path().'/configuration/platform_setting.class.php';
ini_set('include_path',realpath(Path :: get_plugin_path().'pear'));

require_once Path :: get_library_path().'/database/connection.class.php';

// TODO: Move this to a common area since it's used everywhere.
require_once Path :: get_library_path().'session/request.class.php';
require_once Path :: get_library_path().'session/session.class.php';
require_once Path :: get_library_path().'session/cookie.class.php';
require_once Path :: get_library_path().'translation/translation.class.php';
require_once Path :: get_library_path().'html/text.class.php';
require_once Path :: get_library_path().'mail/mail.class.php';
require_once Path :: get_library_path().'html/theme.class.php';
require_once Path :: get_library_path().'html/breadcrumb_trail.class.php';
require_once Path :: get_library_path().'html/breadcrumb.class.php';
require_once Path :: get_library_path().'html/display.class.php';

require_once(Path :: get_admin_path().'lib/admin_data_manager.class.php');
require_once(Path :: get_tracking_path().'lib/events.class.php');
require_once 'MDB2.php';

// Start session

Session :: start($already_installed);

// Test database connection
$connection = Connection :: get_instance()->get_connection();

if (MDB2 :: isError($connection))
{
	die($error_message);
}
unset($error_message);

/*
--------------------------------------------
  DOKEOS CONFIG SETTINGS
--------------------------------------------
*/

if(PlatformSetting :: get('server_type') == 'test')
{
	/*
	--------------------------------------------
	Server type is test
	- high error reporting level
	- only do addslashes on $_GET and $_POST
	--------------------------------------------
	*/
	error_reporting(E_ALL & ~E_NOTICE);

	//Addslashes to all $_GET variables
	foreach($_GET as $key=>$val)
	{
		if(!ini_get('magic_quotes_gpc'))
		{
			if(is_string($val))
			{
				$_GET[$key]=addslashes($val);
			}
		}
	}

	//Addslashes to all $_POST variables
	foreach($_POST as $key=>$val)
	{
		if(!ini_get('magic_quotes_gpc'))
		{
			if(is_string($val))
			{
				$_POST[$key]=addslashes($val);
			}
		}
	}
}
else
{
	/*
	--------------------------------------------
	Server type is not test
	- normal error reporting level
	- full fake register globals block
	--------------------------------------------
	*/
	error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
}

//error_reporting(E_ALL);

/*
 * Handle login and logout
 * (Previously in local.inc.php)
 */

// TODO: Are these includes still necessary ?
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
 
// Login
if(isset($_POST['login']))
{
	$udm = UserDataManager::get_instance();
	$user = $udm->login($_POST['login'],$_POST['password']);
	if(get_class($user) == 'User')
	{
		Session :: register('_uid', $user->get_id());
		Events :: trigger_event('login', 'user', array('server' => $_SERVER, 'user' => $user));
		// TODO: Tracking framework
		//loginCheck($_SESSION['_uid']);
		//event_login();
//		if ($user->is_platform_admin())
//		{
//			// decode all open event informations and fill the track_c_* tables
//			include (api_get_library_path()."/stats.lib.inc.php");
//			decodeOpenInfos();
//		}
	}
	else
	{
		Session :: unregister('_uid');
		header('Location: index.php?loginFailed=1&message=' . $user);
		exit;
	}
}
// Log out
if (isset($_GET['logout']))
{
	$query_string='';
	if(!empty($_SESSION['user_language_choice']))
	{
		$query_string='?language='.$_SESSION['user_language_choice'];
	}
	
	$udm = UserDataManager::get_instance();
	$user = $udm->retrieve_user(Session :: get_user_id());
	
	Events :: trigger_event('logout', 'user', array('server' => $_SERVER, 'user' => $user));
	$udm = UserDataManager::get_instance();
	$udm->logout();
	header("Location: index.php");
	exit();
}

if(isset($_GET['adminuser']))
{
	$_SESSION['_uid'] = $_SESSION['_as_admin'];
	$_SESSION['_as_admin'] = null;
	unset($_SESSION['as_admin']);
}

// ===== "who is logged in?" module section =====

//include_once($includePath."/lib/online.inc.php");
// TODO: Tracking framework
// check and modify the date of user in the track.e.online table
//if (!$x=strpos($_SERVER['PHP_SELF'],'whoisonline.php')) { LoginCheck(isset($_uid) ? $_uid : '',$statsDbName); }

// ===== end "who is logged in?" module section =====

/*
-----------------------------------------------------------
	LOAD LANGUAGE FILES SECTION
-----------------------------------------------------------
*/

//// if we use the javascript version (without go button) we receive a get
//// if we use the non-javascript version (with the go button) we receive a post
//$user_language = Request :: get('language');
//
//if (isset($_POST["language_list"]))
//{
//	$user_language = str_replace("index.php?language=", "", $_POST['language_list']);
//}
//
//// Checking if we have a valid language. If not we set it to the platform language.
//$languages = $adm->retrieve_languages();
//$valid_languages = array();
//while ($language = $languages->next_result())
//{
//	$valid_languages[] = $language->get_folder();	
//}
//
//if (!in_array($user_language, $valid_languages))
//{
//	$user_language = PlatformSetting :: get('platform_language');
//}
//
//
//if (in_array($user_language, $valid_languages) and (isset($_GET['language']) OR isset($_POST['language_list'])))
//{
//		echo $user_language;
//	$user_selected_language = $user_language; // $_GET["language"];
//	$_SESSION['user_language_choice'] = $user_selected_language;
//	$platformLanguage = $user_selected_language;
//}

if (isset($_SESSION['_uid']))
{
	require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
	$language_interface = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id())->get_language();
}
else
{
	$language_interface = PlatformSetting :: get('platform_language');
}

/**
 * Dump functionality with decent output
 */
function dump($variable)
{
	echo '<pre>';
	print_r($variable);
	echo '</pre>';
}
?>