<?php
$langFile = array('weblcms');
$cidReset = true;
$this_section = 'myagenda';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/application/lib/personal_calendar/personal_calendar_manager/personal_calendar.class.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';
if (!api_get_user_id())
{
	api_not_allowed();
}

$usermgr = new UserManager(api_get_user_id());
$user = $usermgr->retrieve_user(api_get_user_id());

$app = new PersonalCalendar($user);
$app->run();
?>