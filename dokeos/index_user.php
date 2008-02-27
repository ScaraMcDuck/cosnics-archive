<?php
$this_section='user';
require_once dirname(__FILE__).'/main/inc/global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';

Translation :: set_application($this_section);

if (!api_get_user_id() && !($_GET['go'] == 'register' || $_GET['go'] == 'reset_password'))
{
	api_not_allowed();
}
if(!api_get_user_id())
{
	$umgr = new UserManager();
}
else
{
	$umgr = new UserManager(api_get_user_id());
}
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