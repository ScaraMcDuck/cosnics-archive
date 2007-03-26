<?php
unset($_SESSION['_uid']);
$cidReset = true;
$langFile = 'searchportal';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/application/lib/search_portal/search_portal_manager/search_portal.class.php';

//$interbredcrump[] = array ('name' => get_lang('SearchPortal'), 'url' => $_SERVER['PHP_SELF']. (!empty ($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : ''));
Display :: display_header();

$app = new SearchPortal();
$app->run();

// TODO: Next lines reconnect to dokeos-database due
// to conflict with DB-connection in repository. This problem
// should be fixed.
mysql_connect($dbHost, $dbLogin, $dbPass);
mysql_select_db($mainDbName);
Display :: display_footer();
?>