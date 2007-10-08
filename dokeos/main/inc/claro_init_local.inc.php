<?php // $Id$
if($_POST['login'])
{
	require_once dirname(__FILE__).'/../../users/lib/usersdatamanager.class.php';
	$udm = UsersDataManager::get_instance();
	$user = $udm->login($_POST['login'],$_POST['password']);
	if(!is_null($user))
	{
		$_SESSION['_uid'] = $user->get_user_id();
	}
	else
	{
		api_session_unregister('_uid');
		header('Location: index.php?loginFailed=1');
		exit;
	}
}
?>