<?php
$this_section = 'home';

include_once ('common/global.inc.php');
require_once Path :: get_user_path() . 'lib/usermanager/usermanager.class.php';
require_once Path :: get_home_path() . 'lib/home_manager/homemanager.class.php';

Translation :: set_application('home');
Theme :: set_application('home');
Theme :: set_theme(PlatformSetting :: get('theme'));

if (PlatformSession :: get_user_id())
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