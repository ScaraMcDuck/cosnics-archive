<?php
$langFile = 'pm';
$cidReset = true;
$this_section = 'mypms';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';
require_once dirname(__FILE__).'/application/lib/personal_messenger/personal_messenger_manager/personal_messenger.class.php';
if (!api_get_user_id())
{
	api_not_allowed();
}

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

$app = new PersonalMessenger($user);
$app->run();
?>