<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage calendar_event
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents a calendar event
 */
class CalendarEvent extends LearningObject
{
	/**
	 * The start date of the calendar event
	 */
	const PROPERTY_START_DATE = 'start_date';
	/**
	 * The end date of the calendar event
	 */
	const PROPERTY_END_DATE = 'end_date';
	/**
	 * Gets the start date of this calendar event
	 * @return int The start date
	 */
	function get_start_date ()
	{
		return $this->get_additional_property(self :: PROPERTY_START_DATE);
	}
	/**
	 * Sets the start date of this calendar event
	 * @param int The start date
	 */
	function set_start_date ($start_date)
	{
		return $this->set_additional_property(self :: PROPERTY_START_DATE, $start_date);
	}
	/**
	 * Gets the end date of this calendar event
	 * @return int The end date
	 */
	function get_end_date ()
	{
		return $this->get_additional_property(self :: PROPERTY_END_DATE);
	}
	/**
	 * Sets the end date of this calendar event
	 * @param int The end date
	 */
	function set_end_date ($end_date)
	{
		return $this->set_additional_property(self :: PROPERTY_END_DATE, $end_date);
	}
	/**
	 * Attachments are supported by calendar events
	 * @return boolean Always true
	 */
	function supports_attachments()
	{
		return true;
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_START_DATE, self :: PROPERTY_END_DATE);
	}
}
?>