<?php
/**
 * $Id:$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../personalcalendarpublishercomponent.class.php';
/**
 * Browser component of the personal calendar event publisher. This component
 * can be used to browse the available calendar events in the repository and
 * select events to publish in the personal calendar.
 */
class PersonalCalendarBrowser extends PersonalCalendarPublisherComponent
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