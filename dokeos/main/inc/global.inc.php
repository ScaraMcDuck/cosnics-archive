<?php // $Id$
/**
==============================================================================
* It is recommended that ALL dokeos scripts include this important file.
* This script manages
* - http get, post, post_files, session, server-vars extraction into global namespace;
*   (which doesn't occur anymore when servertype config setting is set to test,
*    and which will disappear completely in Dokeos 1.6.1)
* - include of /conf/claro_main.conf.php and /lib/main_api.lib.php;
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
$main_configuration_file_path = $includePath . '/../../common/configuration/configuration.php';
$already_installed = false;
if (file_exists($main_configuration_file_path))
{
	require_once($main_configuration_file_path);
	$already_installed = true;
}
// include the main Dokeos platform library file
require_once($includePath.'/lib/main_api.lib.php');
// TODO: Move this to a common area since it's used everywhere.
require_once dirname(__FILE__).'/../../common/filesystem/path.class.php';
require_once(dirname(__FILE__).'/../../common/configuration/configuration.class.php');
require_once(dirname(__FILE__).'/../../common/session/platformsession.class.php');
require_once(dirname(__FILE__).'/../../common/translation/translation.class.php');

// Add the path to the pear packages to the include path
ini_set('include_path',realpath(Path :: get_path(SYS_PLUGIN_PATH).'pear'));

// Include the libraries that are necessary everywhere
require_once(api_get_library_path().'/database.lib.php');
require_once(api_get_library_path().'/display.lib.php');
require_once(api_get_library_path().'/role_right.lib.php');

require_once(dirname(__FILE__).'/../../admin/lib/admindatamanager.class.php');
require_once 'MDB2.php';

// Start session

PlatformSession :: platform_session_start($already_installed);

$error_message = <<<EOM
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>Dokeos not installed!</title>
		<link rel="stylesheet" href="main/css/default.css" type="text/css"/>
	</head>
	<body>
		<div id="header1">Dokeos not installed!</div>
		<div style="text-align: center;"><br /><br />
				<form action="main/install/index.php" method="get"><input type="submit" value="&nbsp;&nbsp; Click to INSTALL DOKEOS &nbsp;&nbsp;" /></form><br />
				or <a href="documentation/installation_guide.html" target="_blank">read the installation guide</a><br /><br />
		</div>
		<div id="footer">
			<div class="copyright">Platform <a href="http://www.dokeos.com"> Dokeos </a> &copy; 2007 </div>
			&nbsp;
		</div>
	</body>
</html>
EOM;

// 
if (!$already_installed)
{
	die($error_message);
}

// Test database connection
$conf = Configuration :: get_instance();
$connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string'));

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

$adm = AdminDataManager :: get_instance();

$server_type = $adm->retrieve_setting_from_variable_name('server_type');
if($server_type->get_value() == 'test')
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

	if(!isset($HTTP_GET_VARS)) { $HTTP_GET_VARS=$_GET; }
	if(!isset($HTTP_POST_VARS)) { $HTTP_POST_VARS=$_POST; }
	if(!isset($HTTP_POST_FILES)) { $HTTP_POST_FILES=$_FILES; }
	if(!isset($HTTP_SESSION_VARS)) { $HTTP_SESSION_VARS=$_SESSION; }
	if(!isset($HTTP_SERVER_VARS)) { $HTTP_SERVER_VARS=$_SERVER; }

	// Register GET variables into $GLOBALS
	if(sizeof($HTTP_GET_VARS))
	{
		$_GET=array();

		foreach($HTTP_GET_VARS as $key=>$val)
		{
			if(!ini_get('magic_quotes_gpc'))
			{
				if(is_string($val))
				{
					$HTTP_GET_VARS[$key]=addslashes($val);
				}
			}

			$_GET[$key]=$HTTP_GET_VARS[$key];

			if(!isset($_SESSION[$key]) && $key != 'includePath')
			{
				$GLOBALS[$key]=$HTTP_GET_VARS[$key];
			}
		}
	}

	// Register POST variables into $GLOBALS
	if(sizeof($HTTP_POST_VARS))
	{
		$_POST=array();

		foreach($HTTP_POST_VARS as $key=>$val)
		{
			if(!ini_get('magic_quotes_gpc'))
			{
				if(is_string($val))
				{
					$HTTP_POST_VARS[$key]=addslashes($val);
				}
			}

			$_POST[$key]=$HTTP_POST_VARS[$key];

			if(!isset($_SESSION[$key]) && $key != 'includePath')
			{
				$GLOBALS[$key]=$HTTP_POST_VARS[$key];
			}
		}
	}

	if(sizeof($HTTP_POST_FILES))
	{
		$_FILES=array();

		foreach($HTTP_POST_FILES as $key=>$val)
		{
			$_FILES[$key]=$HTTP_POST_FILES[$key];

			if(!isset($_SESSION[$key]) && $key != 'includePath')
			{
				$GLOBALS[$key]=$HTTP_POST_FILES[$key];
			}
		}
	}

	// Register SESSION variables into $GLOBALS
	if(sizeof($HTTP_SESSION_VARS))
	{
		if(!is_array($_SESSION))
		{
			$_SESSION=array();
		}

		foreach($HTTP_SESSION_VARS as $key=>$val)
		{
			$_SESSION[$key]=$HTTP_SESSION_VARS[$key];
			$GLOBALS[$key]=$HTTP_SESSION_VARS[$key];
		}
	}

	// Register SERVER variables into $GLOBALS
	if(sizeof($HTTP_SERVER_VARS))
	{
		$_SERVER=array();
		foreach($HTTP_SERVER_VARS as $key=>$val)
		{
			$_SERVER[$key]=$HTTP_SERVER_VARS[$key];

			if(!isset($_SESSION[$key]) && $key != 'includePath')
			{
				$GLOBALS[$key]=$HTTP_SERVER_VARS[$key];
			}
		}
	}
}

/*
 * Handle login and logout
 * (Previously in local.inc.php)
 */

// TODO: Are these includes still necessary ?
require_once (api_get_library_path().'/online.inc.php');
require_once (api_get_library_path().'/events.lib.inc.php');
require_once dirname(__FILE__).'/../../users/lib/usersdatamanager.class.php';
 
// Login
if($_POST['login'])
{
	$udm = UsersDataManager::get_instance();
	$user = $udm->login($_POST['login'],$_POST['password']);
	if(!is_null($user))
	{
		PlatformSession :: platform_session_register('_uid', $user->get_user_id());
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
		PlatformSession :: platform_session_unregister('_uid');
		header('Location: index.php?loginFailed=1');
		exit;
	}
}
// Log out
if ($_GET['logout'])
{
	$query_string='';
	if(!empty($_SESSION['user_language_choice']))
	{
		$query_string='?language='.$_SESSION['user_language_choice'];
	}
	// TODO: Reimplement tracking
	//LoginDelete($uid, $statsDbName);
	$udm = UsersDataManager::get_instance();
	$udm->logout();
	header("Location: index.php");
	exit();
}

// ===== "who is logged in?" module section =====

include_once($includePath."/lib/online.inc.php");
// TODO: Tracking framework
// check and modify the date of user in the track.e.online table
//if (!$x=strpos($_SERVER['PHP_SELF'],'whoisonline.php')) { LoginCheck(isset($_uid) ? $_uid : '',$statsDbName); }

// ===== end "who is logged in?" module section =====

/*
-----------------------------------------------------------
	LOAD LANGUAGE FILES SECTION
-----------------------------------------------------------
*/

// if we use the javascript version (without go button) we receive a get
// if we use the non-javascript version (with the go button) we receive a post
$user_language = $_GET["language"];

if ($_POST["language_list"])
	{
	$user_language = str_replace("index.php?language=","",$_POST["language_list"]);
	}

// Checking if we have a valid language. If not we set it to the platform language.
$languages = $adm->retrieve_languages();
$valid_languages = array();
while ($language = $languages->next_result())
{
	$valid_languages[] = $language->get_english_name();	
}

if (!in_array($user_language,$valid_languages['folder']))
{
	$user_language=$adm->retrieve_setting_from_variable_name('platform_language', 'admin')->get_value();
}


if (in_array($user_language,$valid_languages['folder']) and (isset($_GET['language']) OR isset($_POST['language_list'])))
{
	$user_selected_language = $user_language; // $_GET["language"];
	$_SESSION["user_language_choice"] = $user_selected_language;
	$platformLanguage = $user_selected_language;
}

if (isset($_SESSION['_uid']))
{
	require_once dirname(__FILE__).'/../../users/lib/usermanager/usermanager.class.php';
	$usermgr = new UserManager($_SESSION['_uid']);
	$language_interface = $usermgr->get_user()->get_language();
}
else
{
	$language_interface = $adm->retrieve_setting_from_variable_name('platform_language', 'admin')->get_value();
}

api_use_lang_files('trad4all', 'notification');
?>