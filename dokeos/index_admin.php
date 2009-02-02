<?php
$this_section = 'admin';
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_admin_path().'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_user_path().'lib/user_manager/user_manager.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

/*if (!Authentication :: is_valid())
{
	Display :: not_allowed();
}*/

$usermgr = new UserManager(Session :: get_user_id());
$user = $usermgr->retrieve_user(Session :: get_user_id());

/*if (!$user->is_platform_admin())
{
	Display :: not_allowed();
}*/

$app = new AdminManager($user);
$app->run();
?>