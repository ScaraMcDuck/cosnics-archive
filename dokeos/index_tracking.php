<?php
/**
 * Start webinterface
 */

$cidReset = true;
$this_section = 'tracker';

require_once dirname(__FILE__) . '/common/global.inc.php';
require_once Path :: get_tracking_path() . '/lib/tracking_manager/trackingmanager.class.php';
require_once Path :: get_user_path() . 'lib/usermanager/usermanager.class.php';

$language_interface = 'english';

Translation :: set_application($this_section);

if (!PlatformSession :: get_user_id())
{
	Display :: display_not_allowed();
}

$usermgr = new UserManager(PlatformSession :: get_user_id());
$user = $usermgr->retrieve_user(PlatformSession :: get_user_id());

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