<?php
$this_section = 'home';

include_once ('common/global.inc.php');
require_once Path :: get_user_path() . 'lib/user_manager/user_manager.class.php';
require_once Path :: get_home_path() . 'lib/home_manager/home_manager.class.php';

Translation :: set_application('home');
Theme :: set_application('home');

if (Session :: get_user_id())
{
	$usermgr = new UserManager($_SESSION['_uid']);
	$user = $usermgr->get_user();
}
else
{
	$user = null;
}

$hmgr = new HomeManager($user);

$hmgr->render_menu('home');
?>
