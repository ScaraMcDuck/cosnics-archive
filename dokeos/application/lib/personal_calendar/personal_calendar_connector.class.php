<?php
/**
 * $Id: weblcms.class.php 11636 2007-03-21 14:17:01Z Scara84 $
 * @package application.personal_calendar
 */
/**
 * Implementations of this interface provide the functionality to connect to
 * other applications and to retrieve calendar events from those applications.
 * This way, you can easily display calendar events which are published in other
 * applications in the personal calendar.
 */
interface PersonalCalendarConnector
{
	/**
	 * Gets the calendar events published in the application associated with
	 * this personal calendar connector.
	 * This function will return all calendar events of which at least a part of
	 * the event takes place between the given boundaries (inclusive).
	 * @param int $from_date
	 * @param int $to_date
	 * @return array An array of LearningObjectPublicationAttributes objects.
	 */
	public function get_events($from_date, $to_date);
}
?>