<?php
$this_section = 'home';

require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_home_path() . 'lib/home_manager/home_manager.class.php';
require_once Path :: get_user_path() . 'lib/usermanager/user_manager.class.php';

Translation :: set_application('home');
Theme :: set_application($this_section);

if (Session :: get_user_id())
{
	$usermgr = new UserManager(Session :: get_user_id());
	$user = $usermgr->get_user();
}
else
{
	$user = null;
}

$app = new HomeManager($user);

try
{
	$app->run();
}
catch(Exception $exception)
{
	$app->display_header();
	Display::display_error_message($exception->getMessage());
	$app->display_footer();
}
?>