<?php
$langFile = 'weblcms';
$this_section = 'mycourses';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/application/lib/weblcms/weblcms_manager/weblcms.class.php';

// TODO: Move this somewhere where it makes sense.
//api_protect_course_script();

if (!api_get_user_id())
{
	api_not_allowed();
}

$app = new Weblcms();
$app->run();
?>