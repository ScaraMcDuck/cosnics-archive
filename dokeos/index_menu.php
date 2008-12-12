<?php
$this_section = 'menu';

require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_menu_path() . 'lib/menu_manager/menu_manager.class.php';
require_once Path :: get_user_path() . 'lib/user_manager/user_manager.class.php';

Translation :: set_application('menu');
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

$app = new MenuManager($user);

try
{
	$app->run();
}
catch(Exception $exception)
{
	$app->display_header();
	Display :: error_message($exception->getMessage());
	$app->display_footer();
}
?>