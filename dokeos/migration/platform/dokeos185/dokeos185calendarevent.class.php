<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importcalendarevent.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/announcement/announcement.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/category/category.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Calendar Event
 *
 * @author Sven Vanpoucke
 */
class Dokeos185CalendarEvent extends ImportCalendarEvent
{
	/**
	 * Migration data manager
	 */
	private static $mgdm;

	/**
	 * Announcement properties
	 */	 
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_CONTENT = 'content';
	const PROPERTY_START_DATE = 'start_date';
	const PROPERTY_END_DATE = 'end_date';
	
	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new dokeos185 Calender Event object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185CalendarEvent($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_CONTENT,
					  self :: PROPERTY_START_DATE, self :: PROPERTY_END_DATE);
	}
	
	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Returns the id of this calendar event.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	 
	/**
	 * Returns the title of this calendar event.
	 * @return string the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	/**
	 * Returns the content of this calendar event.
	 * @return string the content.
	 */
	function get_content()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT);
	}
	
	/**
	 * Returns the start_date of this calendar event.
	 * @return date the start_date.
	 */
	function get_start_date()
	{
		return $this->get_default_property(self :: PROPERTY_START_DATE);
	}
	
	/**
	 * Returns the end_date of this calendar event.
	 * @return date the end_date.
	 */
	function get_end_date()
	{
		return $this->get_default_property(self :: PROPERTY_END_DATE);
	}
	
	/**
	 * Sets the id of this calendar event.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the title of this calendar event.
	 * @param string $title The title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	
	/**
	 * Sets the content of this calendar event.
	 * @param string $content The content
	 */
	function set_content($content)
	{
		$this->set_default_property(self :: PROPERTY_CONTENT, $content);
	}
	
	/**
	 * Sets the start_date of this calendar event.
	 * @param string $start_date The start_date
	 */
	function set_start_date($start_date)
	{
		$this->set_default_property(self :: PROPERTY_START_DATE, $start_date);
	}
	
	/**
	 * Sets the end_date of this calendar event.
	 * @param string $end_date The end_date
	 */
	function set_end_date($end_date)
	{
		$this->set_default_property(self :: PROPERTY_END_DATE, $end_date);
	}
	
	function is_valid_calendar_event()
	{
		
	}
	
	function convert_to_new_calendar_event()
	{
		
	}
	
	function get_all_calendar_events($course, $mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_calendar_events();
	}
}
?>
