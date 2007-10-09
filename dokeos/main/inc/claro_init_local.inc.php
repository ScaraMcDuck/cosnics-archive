<?php // $Id$
include_once (api_get_library_path().'/online.inc.php');
require_once dirname(__FILE__).'/../../users/lib/usersdatamanager.class.php';
if($_POST['login'])
{
	$udm = UsersDataManager::get_instance();
	$user = $udm->login($_POST['login'],$_POST['password']);
	if(!is_null($user))
	{
		$_SESSION['_uid'] = $user->get_user_id();
		loginCheck($_SESSION['_uid']);
	}
	else
	{
		api_session_unregister('_uid');
		header('Location: index.php?loginFailed=1');
		exit;
	}
}
if ($_GET['logout'])
{
	$query_string='';
	if(!empty($_SESSION['user_language_choice']))
	{
		$query_string='?language='.$_SESSION['user_language_choice'];
	}
	LoginDelete($uid, $statsDbName);
	$udm = UsersDataManager::get_instance();
	$udm->logout();
	header("Location: index.php$query_string");
	exit();
}
?>