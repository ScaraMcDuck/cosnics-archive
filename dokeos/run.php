<?php
/**
 * $Id: application.class.php 12019 2007-04-13 12:57:10Z Scara84 $
 * @package application
 */
/**
 * This script will load the requested application and call its run() function.
 */
$application_key = $_GET['application'];
foreach(explode('_',$application_key) as $key => $value)
{
	$application_class .= ucfirst($value);
}
$this_section = $application_key;
$application_path = dirname(__FILE__).'/application/lib/'.$application_key.'/'.$application_key.'_manager/'.$application_key.'.class.php';

require_once dirname(__FILE__).'/main/inc/global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';

// If application path doesn't exist, block the user
if(!file_exists($application_path))
{
	api_not_allowed();
}

require_once $application_path;

Translation :: set_application($this_section);
api_use_lang_files($application_key);
// TODO: Move this somewhere where it makes sense.
//api_protect_course_script();
if (!api_get_user_id())
{
	api_not_allowed();
}
// Load the current user
$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());
// Load & run the application
$app = new $application_class($user);
$app->set_parameter('application',$application_key);
$app->run();
?>