<?php
$langFile = 'admin';
require_once dirname(__FILE__).'/claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/application/lib/search_portal/search_portal.class.php';
include_once (api_get_library_path()."/formvalidator/FormValidator.class.php");

unset($_SESSION['_user']);

// TODO: Move this inside the application, so breadcrumbs can be set etc.
Display::display_header(api_get_setting('siteName'));

$app = new SearchPortal();
$app->run();

// TODO: Next lines reconnect to dokeos-database due
// to conflict with DB-connection in repository. This problem
// should be fixed.
mysql_connect($dbHost,$dbLogin,$dbPass);
mysql_select_db($mainDbName);
Display::display_footer();
?>