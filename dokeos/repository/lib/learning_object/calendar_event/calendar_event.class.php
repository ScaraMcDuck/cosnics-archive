<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package repository.learningobject
 * @subpackage calendar_event
 */
class CalendarEvent extends LearningObject
{
	const PROPERTY_START_DATE = 'start_date';
	const PROPERTY_END_DATE = 'end_date';

	function get_start_date ()
	{
		return $this->get_additional_property(self :: PROPERTY_START_DATE);
	}
	function set_start_date ($start_date)
	{
		return $this->set_additional_property(self :: PROPERTY_START_DATE, $start_date);
	}
	function get_end_date ()
	{
		return $this->get_additional_property(self :: PROPERTY_END_DATE);
	}
	function set_end_date ($end_date)
	{
		return $this->set_additional_property(self :: PROPERTY_END_DATE, $end_date);
	}
}
?>