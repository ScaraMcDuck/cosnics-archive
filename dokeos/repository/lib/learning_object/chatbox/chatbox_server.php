<?php
/**
 * @package repository.learningobject
 * @subpackage chatbox
 */
// Work needed here to log messages etc...
require_once('../../../../common/global.inc.php');
$rdm = RepositoryDataManager::get_instance();
$chatbox = $rdm->retrieve_learning_object($_GET['chatbox']);
if(isset($_GET['message']))
{
	$user_id = Session :: get_user_id();
	$usermgr = new UserManager($user_id);
	$user = $usermgr->retrieve_user($user_id);
	$chatbox->add_message($user,$_GET['message']);
}
echo $chatbox->get_messages();
?>