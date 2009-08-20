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
    require_once ($main_configuration_file_path);
    $already_installed = true;
}

$error_message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>Dokeos isn\'t installed ?!</title>
		<link rel="stylesheet" href="layout/aqua/css/common.css" type="text/css"/>
	</head>
	<body dir="ltr">
		<div id="outerframe">
			<div id="header">
				<div id="header1">
					<div class="banner"><div class="logo"></div></div>
					<div class="clear">&nbsp;</div>
				</div>
				<div class="clear">&nbsp;</div>
			</div>
	
			<div id="main" style="min-height: 300px;">' . "\n";

$version = phpversion();

if ($version >= 5.2)
{
    $error_message .= '				<div class="normal-message" style="margin-bottom: 39px; margin-top: 30px;">From the looks of it, Dokeos 2.0 is currently not installed on your system.<br /><br />Please check your database and/or configuration files if you are certain the platform was installed correctly.<br /><br />If you\'re starting Dokeos for the first time, you may want to install the platform first by clicking the button below. Alternatively, you can read the installation guide, visit DokeosPlanet.org for more information or go to the community forum if you need support.
				</div>
				<div class="clear">&nbsp;</div>
				<div style="text-align: center;"><a class="button positive_button" href="install/">Install Dokeos 2.0</a><a class="button normal_button read_button" href="documentation/installation_guide.html">Read the installation guide</a><a class="button normal_button surf_button" href="http://www.dokeosplanet.org/">Visit DokeosPlanet.org</a><a class="button normal_button help_button" href="http://www.dokeosplanet.org/forum/">Get support</a></div>' . "\n";
}
else
{
    $error_message .= '				<div class="error-message">Your version of PHP is not recent enough to use dokeos 2.0.
					   <br /><a href="http://www.php.net">Please upgrade to PHP version 5.2 or higher</a></div><br /><br />' . "\n";
}


$error_message .= '			</div>
	
			<div id="footer">
				<div id="copyright">
					<div class="logo">
					<a href="http://www.dokeosplanet.org"><img src="layout/aqua/img/common/dokeos_logo_small.png" /></a>
					</div>
					<div class="links">
						<a href="http://www.dokeosplanet.org">http://www.dokeosplanet.org</a>&nbsp;|&nbsp;&copy;&nbsp;2009
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</body>
</html>';

//
if (! $already_installed)
{
    die($error_message);
}

// Add the path to the pear packages to the include path
require_once dirname(__FILE__) . '/configuration/configuration.class.php';
require_once dirname(__FILE__) . '/filesystem/path.class.php';
require_once Path :: get_library_path() . '/configuration/platform_setting.class.php';
ini_set('include_path', realpath(Path :: get_plugin_path() . 'pear'));

require_once Path :: get_library_path() . '/database/connection.class.php';

// TODO: Move this to a common area since it's used everywhere.
require_once Path :: get_library_path() . 'session/request.class.php';
require_once Path :: get_library_path() . 'session/session.class.php';
require_once Path :: get_library_path() . 'session/cookie.class.php';
require_once Path :: get_library_path() . 'translation/translation.class.php';
require_once Path :: get_library_path() . 'hashing/hashing.class.php';
require_once Path :: get_library_path() . 'html/text.class.php';
require_once Path :: get_library_path() . 'mail/mail.class.php';
require_once Path :: get_library_path() . 'html/theme.class.php';
require_once Path :: get_library_path() . 'html/breadcrumb_trail.class.php';
require_once Path :: get_library_path() . 'html/breadcrumb.class.php';
require_once Path :: get_library_path() . 'html/display.class.php';
require_once Path :: get_library_path() . 'html/header.class.php';
require_once Path :: get_help_path() . 'lib/help_manager/help_manager.class.php';

require_once (Path :: get_admin_path() . 'lib/admin_data_manager.class.php');
require_once (Path :: get_tracking_path() . 'lib/events.class.php');
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

if (PlatformSetting :: get('server_type') == 'test')
{
    /*
	--------------------------------------------
	Server type is test
	- high error reporting level
	- only do addslashes on $_GET and $_POST
	--------------------------------------------
	*/
    error_reporting(E_ALL & ~ E_NOTICE);

    //Addslashes to all $_GET variables
    foreach ($_GET as $key => $val)
    {
        if (! ini_get('magic_quotes_gpc'))
        {
            if (is_string($val))
            {
                //Request :: set_get($key,addslashes($val))
                Request :: set_get($key, addslashes($val));
            }
        }
    }

    //Addslashes to all $_POST variables
    foreach ($_POST as $key => $val)
    {
        if (! ini_get('magic_quotes_gpc'))
        {
            if (is_string($val))
            {
                $_POST[$key] = addslashes($val);
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

/*
 * Handle login and logout
 * (Previously in local.inc.php)
 */

// TODO: Are these includes still necessary ?
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';

// Login
if (isset($_POST['login']))
{
    $udm = UserDataManager :: get_instance();
    $user = $udm->login($_POST['login'], $_POST['password']);
    if (get_class($user) == 'User')
    {
        Session :: register('_uid', $user->get_id());
        Events :: trigger_event('login', 'user', array('server' => $_SERVER, 'user' => $user));

        $request_uri = Session :: retrieve('request_uri');

        if ($request_uri)
        {
            $request_uris = explode("/", $request_uri);
            $request_uri = array_pop($request_uris);
            header('Location: ' . $request_uri);
        }

        $login_page = PlatformSetting :: get('page_after_login');
        if ($login_page == 'weblcms')
        {
            header('Location: run.php?application=weblcms');
        }
    }
    else
    {
        Session :: unregister('_uid');
        header('Location: index.php?loginFailed=1&message=' . $user);
        exit();
    }
}
else
{
    Session :: unregister('request_uri');
}

set_error_handler(handle_error);

// Log out
if (Request :: get('logout'))
{
    $query_string = '';
    if (! empty($_SESSION['user_language_choice']))
    {
        $query_string = '?language=' . $_SESSION['user_language_choice'];
    }

    $user_id = Session :: get_user_id();

    if (isset($user_id))
    {
        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user(Session :: get_user_id());

        $udm = UserDataManager :: get_instance();
        $udm->logout();
        Events :: trigger_event('logout', 'user', array('server' => $_SERVER, 'user' => $user));
    }

    header("Location: index.php");
    exit();
}
//unset($_SESSION['_uid']);


if (Request :: get('adminuser'))
{
    $_SESSION['_uid'] = $_SESSION['_as_admin'];
    $_SESSION['_as_admin'] = null;
    unset($_SESSION['as_admin']);
}

$user = Session :: get_user_id();
if ($user)
{
    Events :: trigger_event('online', 'admin', array('user' => $user));
}

$language_interface = PlatformSetting :: get('platform_language');

if (isset($_SESSION['_uid']))
{
    require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
    $user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());

    if ($user)
    {
        $language_interface = $user->get_language();
    }

    if (strpos($_SERVER['REQUEST_URI'], 'leave.php') === false && strpos($_SERVER['REQUEST_URI'], 'ajax') === false)
    {
        $return = Events :: trigger_event('enter', 'user', array('location' => $_SERVER['REQUEST_URI'], 'user' => $user, 'event' => 'enter'));
        $htmlHeadXtra[] = '<script language="JavaScript" type="text/javascript">var tracker=' . $return[0] . '</script>';
    }
}

/**
 * Dump functionality with decent output
 */
function dump($variable)
{
    echo '<pre style="background-color: white; color: black; padding: 5px; margin: 0px;">';
    print_r($variable);
    echo '</pre>';
}

/**
 * Error handling function
 */
function handle_error($errno, $errstr, $errfile, $errline)
{
	switch ($errno) 
	{
    case E_USER_ERROR:
        write_error('PHP Fatal error', $errstr, $errfile, $errline);
        break;
    case E_USER_WARNING:
        write_error('PHP Warning', $errstr, $errfile, $errline);
        break;
    case E_USER_NOTICE:
        write_error('PHP Notice', $errstr, $errfile, $errline);
    }

    return true;
}

function write_error($errno, $errstr, $errfile, $errline)
{
	$path = Path :: get(SYS_FILE_PATH) . 'logs';
	$file = $path . '/error_log_' . date('Ymd') . '.txt';
	$fh = fopen($file, 'a');
	
	$message = date('[H:i:s] ', time()) . $errno . ' File: ' . $errfile . ' - Line: ' . $errline . ' - Message: ' . $errstr;
	
	fwrite($fh, $message . "\n");
	fclose($fh);
}
?>