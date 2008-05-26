<?php
$this_section = 'myrepository';
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_repository_path(). 'lib/repository_manager/repository_manager.class.php';
require_once Path :: get_user_path(). 'lib/usermanager/usermanager.class.php';

Translation :: set_application('repository');
Theme :: set_application('repository');

if (!Authentication :: is_valid())
{
	Display :: display_not_allowed();
}

$usermgr = new UserManager(Session :: get_user_id());
$user = $usermgr->retrieve_user(Session :: get_user_id());

$repmgr = new RepositoryManager($user);
try
{
	$repmgr->run();
}
catch(Exception $exception)
{
	$repmgr->display_header();
	Display::display_error_message($exception->getMessage());
	$repmgr->display_footer();
}
?>