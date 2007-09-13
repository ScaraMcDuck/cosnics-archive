<?php
$this_section = 'myportfolio';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/application/lib/myportfolio/myportfolio_manager/myportfolio.class.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';
if (!api_get_user_id())
{
	api_not_allowed();
}

api_use_lang_files('portfolio');

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

$app = new MyPortfolio($user);
$app->run();
?>