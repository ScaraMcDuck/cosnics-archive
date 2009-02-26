<?php
$this_section = 'webservice';
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_user_path(). 'lib/user_manager/user_manager.class.php';
require_once Path :: get_webservice_path(). 'lib/webservice_manager/webservice_manager.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (!Authentication :: is_valid())
{
	Display :: not_allowed();
}

$usermgr = new UserManager(Session :: get_user_id());
$user = $usermgr->retrieve_user(Session :: get_user_id());

$wsmgr = new WebserviceManager($user);
/*$wsmgr->display_header();
$wsmgr->display_footer();*/

try
{
	$wsmgr->run();
}
catch(Exception $exception)
{
	$wsmgr->display_header();
	Display :: error_message($exception->getMessage());
	$wsmgr->display_footer();
}

?>