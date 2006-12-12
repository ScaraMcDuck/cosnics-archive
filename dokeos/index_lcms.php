<?php
$langFile = 'weblcms';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/application/lib/weblcms/weblcms.class.php';

// TODO: Move this somewhere where it makes sense.
api_protect_course_script();

$app = new Weblcms();
$app->run();
?>