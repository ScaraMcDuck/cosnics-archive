<?php
$this_section='group';
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_group_path(). 'lib/group_manager/group_manager.class.php';
require_once Path :: get_user_path(). 'lib/user_manager/user_manager.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (!Authentication :: is_valid())
{
	Display :: not_allowed();
}

$usermgr = new UserManager(Session :: get_user_id());
$user = $usermgr->retrieve_user(Session :: get_user_id());

$cgmgr = new GroupManager($user);
try
{
	$cgmgr->run();
}
catch(Exception $exception)
{
	$cgmgr->display_header();
	Display :: error_message($exception->getMessage());
	$cgmgr->display_footer();
}
?>