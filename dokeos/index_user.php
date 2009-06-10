<?php
$this_section = 'user';
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_user_path(). 'lib/user_manager/user_manager.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (!Session :: get_user_id() && !(Request :: get('go') == 'register' || Request :: get('go') == 'reset_password'))
{
	Display :: not_allowed();
}
if(!Session :: get_user_id())
{
	$umgr = new UserManager();
}
else
{
	$umgr = new UserManager(Session :: get_user_id());
}
try
{
	$umgr->run();
}
catch(Exception $exception)
{
	$umgr->display_header();
	Display :: error_message($exception->getMessage());
	$umgr->display_footer();
}
?>