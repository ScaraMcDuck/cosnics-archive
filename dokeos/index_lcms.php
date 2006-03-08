<?php
require_once dirname(__FILE__).'/claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/application/lib/weblcms/weblcms.class.php';

// TODO: Move this somewhere where it makes sense.
api_protect_course_script();

Display::display_header(api_get_setting('siteName'));

$app = new WebLCMS();
$app->run();

Display::display_footer();
?>