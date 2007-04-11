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
	 * The time of the moment to render
	 */
	private $display_time;
	/**
	 * Constructor
	 * @param PersonalCalendar $personal_calendar
	 * @param int $display_time
	 */
	function PersonalCalendarRenderer($personal_calendar,$display_time)
	{
		$this->personal_calendar = $personal_calendar;
		$this->display_time = $display_time;
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
	 * Gets the time
	 * @return int
	 */
	public function get_time()
	{
		return $this->display_time;
	}
	/**
	 * Gets the personal calendar object in which this renderer is used
	 * @return PersonalCalendar
	 */
	public function get_parent()
	{
		return $this->personal_calendar;
	}
	/**
	 *
	 */
	public function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->personal_calendar->get_url($parameters, $encode, $filter, $filterOn);
	}
	/**
	 * Renders the calendar
	 * @return string A html representation of the events in this calendar.
	 */
	abstract function render();
}
?>