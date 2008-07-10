<?php
$this_section='classgroup';
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_class_group_path(). 'lib/class_group_manager/class_group_manager.class.php';
require_once Path :: get_user_path(). 'lib/usermanager/user_manager.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (!Authentication :: is_valid())
{
	Display :: display_not_allowed();
}

$usermgr = new UserManager(Session :: get_user_id());
$user = $usermgr->retrieve_user(Session :: get_user_id());

$cgmgr = new ClassgroupManager($user);
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