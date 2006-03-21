<?php
require_once dirname(__FILE__).'/claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/application/lib/weblcms/weblcms.class.php';

// TODO: Move this somewhere where it makes sense.
api_protect_course_script();

// TODO: Move this inside the application, so breadcrumbs can be set etc.
Display::display_header(api_get_setting('siteName'));

$app = new WebLCMS();
$app->run();

// TODO: Next lines reconnect to dokeos-database due
// to conflict with DB-connection in repository. This problem
// should be fixed.
mysql_connect($dbHost,$dbLogin,$dbPass);
mysql_select_db($mainDbName);
Display::display_footer();
?>