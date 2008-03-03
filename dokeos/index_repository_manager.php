<?php
$cidReset = true;
$this_section = 'myrepository';
require_once dirname(__FILE__).'/common/global.inc.php';
require_once dirname(__FILE__).'/repository/lib/repository_manager/repositorymanager.class.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';

Translation :: set_application('repository');

if (!PlatformSession :: get_user_id())
{
	Display :: display_not_allowed();
}

$usermgr = new UserManager(PlatformSession :: get_user_id());
$user = $usermgr->retrieve_user(PlatformSession :: get_user_id());

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