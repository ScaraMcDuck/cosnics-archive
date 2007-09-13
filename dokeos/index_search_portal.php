<?php
unset($_SESSION['_uid']);
$cidReset = true;
$this_section = 'search_portal';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/application/lib/search_portal/search_portal_manager/search_portal.class.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';

api_use_lang_files('searchportal');

if (!api_get_user_id())
{
	api_not_allowed();
}

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

//$interbredcrump[] = array ('name' => get_lang('SearchPortal'), 'url' => $_SERVER['PHP_SELF']. (!empty ($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : ''));
Display :: display_header();
$app = new SearchPortal($user);
$app->run();

// TODO: Next lines reconnect to dokeos-database due
// to conflict with DB-connection in repository. This problem
// should be fixed.
mysql_connect($dbHost, $dbLogin, $dbPass);
mysql_select_db($mainDbName);
Display :: display_footer();
?>