<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importpersonalagenda.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/calendar_event/calendar_event.class.php';
require_once Path :: get_path(SYS_APP_PATH) . 'lib/personal_calendar/personalcalendarevent.class.php';

/**
 * Class that represents the personal agenda data from dokeos 1.8.5
 * @author Sven Vanpoucke
 */
class Dokeos185PersonalAgenda extends ImportPersonalAgenda
{
	/**
	 ** Migration data manager
	 */
	private static $mgdm;

	/**
	 * personal agenda properties
	 */
	 
	const PROPERTY_ID = 'id';
	const PROPERTY_USER = 'user';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_TEXT = 'text';
	const PROPERTY_DATE = 'date';
	const PROPERTY_ENDDATE = 'enddate';
	const PROPERTY_COURSE = 'course';
	
	/**
	 * Default properties of the personal agenda object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new personal agenda object
	 * @param array $defaultProperties The default properties of the personal agenda
	 *                                 object. Associative array.
	 */
	function Dokeos185PersonalAgenda($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this personal agenda object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this personal agenda.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all personal agenda.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self::PROPERTY_ID, self :: PROPERTY_USER, self :: PROPERTY_TITLE,
		self :: PROPERTY_TEXT, self::PROPERTY_DATE, self::PROPERTY_ENDDATE, self :: PROPERTY_COURSE);
	}
	
	/**
	 * Sets a default property of this personal agenda by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default personal agenda
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	/**
	 * Returns the id of this personal agenda.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the user of this personal agenda
	 * @return int The user ID
	 */
	function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER);
	}
	
	/**
	 * Returns the date of this personal agenda
	 * @return int The date
	 */
	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}
	
	/**
	 * Returns the end date of this personal agenda
	 * @return int The end date
	 */
	function get_enddate()
	{
		return $this->get_default_property(self :: PROPERTY_ENDDATE);
	}
	
	/**
	 * Returns the course of this personal agenda
	 * @return int The course ID
	 */
	function get_course()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE);
	}
	
	/**
	 * Returns the title of this personal agenda
	 * @return string The title
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	/**
	 * Returns the text of this personal agenda
	 * @return string The text
	 */
	function get_text()
	{
		return $this->get_default_property(self :: PROPERTY_TEXT);
	}
	
	/**
	 * Sets the id of this personal agenda.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the user of this personal agenda.
	 * @param int $user The id of the user.
	 */
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}
	
	/**
	 * Sets the title of this personal agenda
	 * @param string $title The title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	
	/**
	 * Sets the text of this personal agenda
	 * @param string $text The text
	 */
	function set_text($text)
	{
		$this->set_default_property(self :: PROPERTY_TEXT, $text);
	}
	
	/**
	 * Sets the date of this personal agenda
	 * @param int $date The date
	 */
	function set_date($date)
	{
		$this->set_default_property(self :: PROPERTY_DATE, $text);
	}
	
	/**
	 * Sets the end date of this personal agenda
	 * @param int $enddate The enddate
	 */
	function set_enddate($enddate)
	{
		$this->set_default_property(self :: PROPERTY_ENDDATE, $text);
	}
	
	/**
	 * Sets the course of this personal agenda
	 * @param int $course The course ID
	 */
	function set_course($course)
	{
		$this->set_default_property(self :: PROPERTY_COURSE, $text);
	}
	
	/**
	 * Check if personal agenda is valid
	 */
	function is_valid_personal_agenda()
	{
		if(!$this->get_user() || (!$this->get_title() && !$this->get_text()) || !$this->get_date() ||
			self :: $mgdm->get_failed_element('dokeos_main.user', $this->get_user()) )
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				'dokeos_main.personal_agenda');
			return false;
		}
		return true;
	}
	
	/**
	 * Migration to new personal agenda
	 */
	function convert_to_new_personal_agenda()
	{
		// Create calendar event	
		$lcms_calendar_event = new CalendarEvent();
		$lcms_calendar_event->set_start_date($this->get_date());
		
		if(!$this->get_enddate())
			$lcms_calendar_event->set_end_date($this->get_date());
		else
			$lcms_calendar_event->set_end_date($this->get_enddate());
			
		if(!$this->get_title())
			$lcms_calendar_event->set_title($this->get_text());
		else
			$lcms_calendar_event->set_title($this->get_title());
		
		if(!$this->get_text())
			$lcms_calendar_event->set_description($this->get_title());
		else
			$lcms_calendar_event->set_description($this->get_text());
		
		//Get owner_ID from
		$owner_id = self :: $mgdm->get_id_reference($this->get_user(), 'user_user');
		if($owner_id)
			$lcms_calendar_event->set_owner_id($owner_id);
		
		//Get repository from user
		$repository_id = self :: $mgdm->get_parent_id($owner_id, 
			'category', Translation :: get_lang('MyRepository'));
		
		$lcms_calendar_event->set_parent_id($repository_id);
		
		$lcms_calendar_event->create();
		
		//Create personal agenda publication
		
		$lcms_personal_calendar = new PersonalCalendarEvent(0, 
			$owner_id, $lcms_calendar_event, $this->get_date());
		
		$lcms_personal_calendar->create_all();
		
		return $lcms_personal_calendar;
		
	}
	
	/** 
	 * Get all personal agendas from database
	 * @param Migration Data Manager $mgdm the datamanager from where the settings should be retrieved;
	 */
	static function get_all_personal_agendas($mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_personal_agendas();	
	}
}
?>
