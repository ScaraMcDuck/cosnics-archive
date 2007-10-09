<?php
/**
 * @package repository.learningobject
 * @subpackage chatbox
 */
// Work needed here to log messages etc...
require_once('../../../../main/inc/claro_init_global.inc.php');
if(!is_array($_SESSION['chat_data']))
{
	$_SESSION['chat_data'] = array();
}
if(isset($_GET['message']))
{
	$usermgr = new UserManager(api_get_user_id());
	$user = $usermgr->retrieve_user(api_get_user_id());
	$_SESSION['chat_data'][] = '<strong>'.$user->get_fullname().':</strong> '.$_GET['message'];
}
echo implode('<br />',$_SESSION['chat_data']);
?>