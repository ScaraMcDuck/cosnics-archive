<?php
/**
 * $Id: calendar_event.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage calendar_event
 */
require_once dirname(__FILE__) . '/../../learningobject.class.php';
/**
 * This class represents a calendar event
 */
class Profile extends LearningObject
{
	/**
	 * The start date of the calendar event
	 */
	const PROPERTY_COMPETENCES = 'competences';
	
	/**
	 * Gets the start date of this calendar event
	 * @return int The start date
	 */
	function get_competences ()
	{
		return $this->get_additional_property(self :: PROPERTY_COMPETENCES);
	}
	/**
	 * Sets the start date of this calendar event
	 * @param int The start date
	 */
	function set_competences ($competences)
	{
		return $this->set_additional_property(self :: PROPERTY_COMPETENCES, $competences);
	}

	/**
	 * Attachments are supported by calendar events
	 * @return boolean Always true
	 */
	function supports_attachments()
	{
		return false;
	}
	
	function is_versionable()
	{
		return false;
	}
}
?>