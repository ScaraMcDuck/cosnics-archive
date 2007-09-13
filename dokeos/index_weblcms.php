<?php
$this_section = 'weblcms';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/application/lib/weblcms/weblcms_manager/weblcms.class.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';

api_use_lang_files('weblcms');

// TODO: Move this somewhere where it makes sense.
//api_protect_course_script();

if (!api_get_user_id())
{
	api_not_allowed();
}

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

$app = new Weblcms($user);
$app->run();
?>