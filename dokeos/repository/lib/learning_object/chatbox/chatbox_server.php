<?php
/**
 * @package repository.learningobject
 * @subpackage chatbox
 */
// Work needed here to log messages etc...
require_once('../../../../main/inc/claro_init_global.inc.php');
$rdm = RepositoryDataManager::get_instance();
$chatbox = $rdm->retrieve_learning_object($_GET['chatbox']);
if(isset($_GET['message']))
{
	$usermgr = new UserManager(api_get_user_id());
	$user = $usermgr->retrieve_user(api_get_user_id());
	$chatbox->add_message($user,$_GET['message']);
}
echo $chatbox->get_messages();
?>