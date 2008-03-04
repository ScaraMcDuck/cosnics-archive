<?php
$this_section='classgroup';
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_classgroup_path(). 'lib/classgroup_manager/classgroupmanager.class.php';
require_once Path :: get_user_path(). 'lib/usermanager/usermanager.class.php';

Translation :: set_application($this_section);

if (!PlatformSession :: get_user_id())
{
	Display :: display_not_allowed();
}

$usermgr = new UserManager(PlatformSession :: get_user_id());
$user = $usermgr->retrieve_user(PlatformSession :: get_user_id());

$cgmgr = new ClassGroupManager($user);
try
{
	$cgmgr->run();
}
catch(Exception $exception)
{
	$cgmgr->display_header();
	Display::display_error_message($exception->getMessage());
	$cgmgr->display_footer();
}
?>