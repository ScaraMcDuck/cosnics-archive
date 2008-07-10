<?php
/**
 * Start webinterface
 */
$this_section = 'tracking';

require_once dirname(__FILE__) . '/common/global.inc.php';
require_once Path :: get_tracking_path() . '/lib/tracking_manager/tracking_manager.class.php';
require_once Path :: get_user_path() . 'lib/usermanager/user_manager.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (!Authentication :: is_valid())
{
	Display :: display_not_allowed();
}

$usermgr = new UserManager(Session :: get_user_id());
$user = $usermgr->retrieve_user(Session :: get_user_id());

$trackmgr = new TrackingManager($user);
try
{
	$trackmgr->run();
}
catch(Exception $exception)
{
	$trackmgr->display_header();
	Display::display_error_message($exception->getMessage());
	$trackmgr->display_footer();
}
?>