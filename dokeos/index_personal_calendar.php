<?php
$langFile = 'weblcms';
$cidReset = true;
$this_section = 'myagenda';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/application/lib/personal_calendar/personal_calendar_manager/personal_calendar.class.php';

$app = new PersonalCalendar(api_get_user_id());
$app->run();
?>