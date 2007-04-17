<?php
$langFile = 'user';
$this_section='SECTION_PLATFORM_ADMIN';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';

if (!api_get_user_id() && !($_GET['go'] == 'register'))
{
	api_not_allowed();
}

$umgr = new UserManager(api_get_user_id());
try
{
	$umgr->run();
}
catch(Exception $exception)
{
	$umgr->display_header();
	Display::display_error_message($exception->getMessage());
	$umgr->display_footer();
}
?>