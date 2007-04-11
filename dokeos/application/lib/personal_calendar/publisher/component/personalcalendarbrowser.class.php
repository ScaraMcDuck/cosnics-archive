<?php
require_once dirname(__FILE__).'/../personalcalendarpublishercomponent.class.php';
class PersonalCalendarBrowser extends PersonalCalendarPublisherComponent
{
	public function as_html()
	{
		return __FILE__;
	}
}

?>