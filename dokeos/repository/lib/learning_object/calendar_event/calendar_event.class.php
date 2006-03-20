<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * @package learningobject.calendarevent
 */
class CalendarEvent extends LearningObject
{
	function get_start_date ()
	{
		return $this->get_additional_property('start_date');
	}
	function set_start_date ($start_date)
	{
		return $this->set_additional_property('start_date', $start_date);
	}
	function get_end_date ()
	{
		return $this->get_additional_property('end_date');
	}
	function set_end_date ($end_date)
	{
		return $this->set_additional_property('end_date', $end_date);
	}
}
?>