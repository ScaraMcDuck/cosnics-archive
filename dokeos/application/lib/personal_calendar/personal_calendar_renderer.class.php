<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
/**
 * A renderer to display a personal calendar to the end user
 */
abstract class PersonalCalendarRenderer
{
	/**
	 * The personal calendar of which the events will be displayed
	 */
	private $personal_calendar;
	/**
	 * Constructor
	 * @param PersonalCalendar $personal_calendar
	 */
	function PersonalCalendarRenderer($personal_calendar)
	{
		$this->personal_calendar = $personal_calendar;
	}
	/**
	 * Gets the evenst to display
	 * @see PersonalCalendar::get_events
	 * @param int $from_date
	 * @param int $to_date
	 */
	public function get_events($from_date,$to_date)
	{
		return $this->personal_calendar->get_events($from_date,$to_date);
	}
	/**
	 * Renders the calendar
	 * @return string A html representation of the events in this calendar.
	 */
	abstract function render();
}
?>