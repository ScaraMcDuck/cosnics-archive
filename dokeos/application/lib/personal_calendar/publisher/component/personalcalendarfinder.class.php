<?php
/**
 * $Id:$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../personalcalendarpublishercomponent.class.php';
/**
 * Finder component of the personal calendar event publisher. This component can
 * be used to search in the repository.
 */
class PersonalCalendarFinder extends PersonalCalendarPublisherComponent
{
	/**
	 * Gets a HTML representation of this component.
	 * @return string
	 * @todo Implmentation
	 */
	public function as_html()
	{
		return __FILE__;
	}
}

?>