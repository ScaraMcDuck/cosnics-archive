<?php
$this_section = 'personal_messenger';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';
require_once dirname(__FILE__).'/application/lib/personal_messenger/personal_messenger_manager/personal_messenger.class.php';
if (!api_get_user_id())
{
	api_not_allowed();
}

api_use_lang_files('personalmessenger');

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

$app = new PersonalMessenger($user);

try
{
	$app->run();
}
catch(Exception $exception)
{
	$app->display_header();
	Display::display_error_message($exception->getMessage());
	$app->display_footer();
}
?>