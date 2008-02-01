<?php // $Id: claro_init_global.inc.php 14105 2008-01-11 14:45:05Z Scara84 $
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
$main_configuration_file_path = $includePath."/conf/config.inc.php";
$already_installed = false;
if (file_exists($main_configuration_file_path)) {
	require_once($main_configuration_file_path);
	$already_installed = true;
}
// include the main Dokeos platform library file
require_once($includePath.'/lib/main_api.lib.php');

// Add the path to the pear packages to the include path
ini_set('include_path',realpath(api_get_path(SYS_PATH).'/plugin/pear'));

// Include the libraries that are necessary everywhere
require_once(api_get_library_path().'/display.lib.php');

require_once(dirname(__FILE__).'/../../admin/lib/admindatamanager.class.php');

// Start session

api_session_start($already_installed);

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

if (!$already_installed) die($error_message);

if(empty($statsDbName) && $already_installed)
{
	$statsDbName=$mainDbName;
}

//$current_settings_table = Database :: get_main_table(MAIN_SETTINGS_CURRENT_TABLE);
//$sql="SELECT * FROM $current_settings_table";
//$result=mysql_query($sql) or die(mysql_error());
//while ($row=mysql_fetch_array($result))
//{
//	if ($row['subkey']==NULL)
//		{ $_setting[$row['variable']]=$row['selected_value']; }
//	else
//		{ $_setting[$row['variable']][$row['subkey']]=$row['selected_value']; }
//}
//// we have to store the settings for the plugins differently because it expects an array
//$sql="SELECT * FROM $current_settings_table WHERE category='plugins'";
//$result=mysql_query($sql) or die(mysql_error());
//while ($row=mysql_fetch_array($result))
//{
//	$key= $row['variable'];
//	if (is_string($_setting[$key]))
//	{
//		$_setting[$key]=array();
//	}
//	$_setting[$key][]=$row['selected_value'];
//	$plugins[$key][]=$row['selected_value'];
//}

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

$user_language = 'english';
$language_interface = 'english';
?>