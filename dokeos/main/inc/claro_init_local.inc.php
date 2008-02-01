<?php // $Id$
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
		$_SESSION['_uid'] = $user->get_user_id();
		// TODO: Tracking framework
		//loginCheck($_SESSION['_uid']);
		//event_login();
		if ($user->is_platform_admin())
		{
			// decode all open event informations and fill the track_c_* tables
			include (api_get_library_path()."/stats.lib.inc.php");
			decodeOpenInfos();
		}
	}
	else
	{
		api_session_unregister('_uid');
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
	header("Location: index.php$query_string");
	exit();
}
?>