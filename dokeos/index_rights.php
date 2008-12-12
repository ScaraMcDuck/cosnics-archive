<?php
$this_section = 'rights';
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_rights_path(). 'lib/rights_manager/rights_manager.class.php';
require_once Path :: get_user_path(). 'lib/user_manager/user_manager.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (!Authentication :: is_valid())
{
	Display :: not_allowed();
}

$usermgr = new UserManager(Session :: get_user_id());
$user = $usermgr->retrieve_user(Session :: get_user_id());

$rmgr = new RightsManager($user);
try
{
	$rmgr->run();
}
catch(Exception $exception)
{
	$rmgr->display_header();
	Display :: error_message($exception->getMessage());
	$rmgr->display_footer();
}
?>