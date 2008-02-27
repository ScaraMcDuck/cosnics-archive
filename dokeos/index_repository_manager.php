<?php
$cidReset = true;
$this_section = 'myrepository';
require_once dirname(__FILE__).'/main/inc/global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/repository/lib/repository_manager/repositorymanager.class.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';

Translation :: set_application('repository');

if (!api_get_user_id())
{
	api_not_allowed();
}

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

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