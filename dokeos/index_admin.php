<?php
$this_section = 'platform_admin';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/admin/lib/admin_manager/admin.class.php';

// TODO: Move this somewhere where it makes sense.
//api_protect_course_script();

api_use_lang_files('admin');

if (!api_get_user_id())
{
	api_not_allowed();
}

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

if (!$user->is_platform_admin())
{
	api_not_allowed();
}

$app = new Admin($user);
$app->run();
?>