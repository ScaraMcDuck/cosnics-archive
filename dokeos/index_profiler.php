<?php
$langFile = array('profiler');
$this_section = 'profiles';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';
require_once dirname(__FILE__).'/application/lib/profiler/profiler_manager/profiler.class.php';
if (!api_get_user_id())
{
	api_not_allowed();
}

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

$app = new Profiler($user);

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