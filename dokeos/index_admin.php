<?php
$this_section = 'admin';
require_once dirname(__FILE__).'/main/inc/global.inc.php';
require_once dirname(__FILE__).'/admin/lib/admin_manager/admin.class.php';

Translation :: set_application($this_section);

if (!api_get_user_id())
{
	Display :: display_not_allowed();
}

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

if (!$user->is_platform_admin())
{
	Display :: display_not_allowed();
}

$app = new Admin($user);
$app->run();
?>