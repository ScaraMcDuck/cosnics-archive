<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importgroup.class.php';
require_once dirname(__FILE__).'/../../../application/lib/weblcms/group/group.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Group (table group_info)
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Group extends ImportGroup
{
	/**
	 * Migration data manager
	 */
	private static $mgdm;

	/**
	 * Group properties
	 */	 
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_CATEGORY_ID = 'category_id';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_MAX_STUDENT = 'max_student';
	const PROPERTY_DOC_STATE = 'doc_state';
	const PROPERTY_CALENDAR_STATE = 'calendar_state';
	const PROPERTY_WORK_STATE = 'work_state';
	const PROPERTY_ANNOUNCEMENTS_STATE = 'groups_state';
	const PROPERTY_SECRET_DIRECTORY = 'secret_directory';
	const PROPERTY_SELF_REGISTRATION_ALLOWED = 'self_registration_allowed';
	const PROPERTY_SELF_UNREGISTRATION_ALLOWED = 'self_unregistration_allowed';
	
	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new dokeos185 Announcement object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Group($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_CATEGORY_ID,
					  self :: PROPERTY_DESCRIPTION, self :: PROPERTY_MAX_STUDENT, 
					  self :: PROPERTY_DOC_STATE, self :: PROPERTY_CALENDAR_STATE, 
					  self :: PROPERTY_WORK_STATE, self :: PROPERTY_ANNOUNCEMENTS_STATE, 
					  self :: PROPERTY_SECRET_DIRECTORY, self :: PROPERTY_SELF_REGISTRATION_ALLOWED,
					  self :: PROPERTY_SELF_UNREGISTRATION_ALLOWED);
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
	 * Returns the id of this group.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	 
	/**
	 * Returns the name of this group.
	 * @return string the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	/**
	 * Returns the category_id of this group.
	 * @return string the category_id.
	 */
	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
	}
	
	/**
	 * Returns the description of this group.
	 * @return date the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	/**
	 * Returns the max_student of this group.
	 * @return int the max_student.
	 */
	function get_max_student()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_STUDENT);
	}
	
	/**
	 * Returns the doc_state of this group.
	 * @return int the doc_state.
	 */
	function get_doc_state()
	{
		return $this->get_default_property(self :: PROPERTY_DOC_STATE);
	}
	
	
	/**
	 * Returns the calendar_state of this announcement.
	 * @return int The calendar_state.
	 */
	function get_calendar_state()
	{
		return $this->get_default_property(self :: PROPERTY_CALENDAR_STATE);
	}
	 
	/**
	 * Returns the work_state of this announcement.
	 * @return string the work_state.
	 */
	function get_work_state()
	{
		return $this->get_default_property(self :: PROPERTY_WORK_STATE);
	}
	
	/**
	 * Returns the announcements_state of this announcement.
	 * @return string the announcements_state.
	 */
	function get_announcements_state()
	{
		return $this->get_default_property(self :: PROPERTY_ANNOUNCEMENTS_STATE);
	}
	
	/**
	 * Returns the secret_directory of this announcement.
	 * @return date the secret_directory.
	 */
	function get_secret_directory()
	{
		return $this->get_default_property(self :: PROPERTY_SECRET_DIRECTORY);
	}
	
	/**
	 * Returns the self_registration_allowed of this announcement.
	 * @return int the self_registration_allowed.
	 */
	function get_self_registration_allowed()
	{
		return $this->get_default_property(self :: PROPERTY_SELF_REGISTRATION_ALLOWED);
	}
	
	/**
	 * Returns the self_unregistration_allowed of this announcement.
	 * @return int the self_unregistration_allowed.
	 */
	function get_self_unregistration_allowed()
	{
		return $this->get_default_property(self :: PROPERTY_SELF_UNREGISTRATION_ALLOWED);
	}
	
	/**
	 * Sets the id of this group.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the name of this group.
	 * @param string $name The name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	/**
	 * Sets the category_id of this group.
	 * @param string $category_id The category_id
	 */
	function set_category_id($category_id)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
	}
	
	/**
	 * Sets the description of this group.
	 * @param string $description The description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	/**
	 * Sets the max_student of this group.
	 * @param string $max_student The max_student
	 */
	function set_max_student($max_student)
	{
		$this->set_default_property(self :: PROPERTY_MAX_STUDENT, $max_student);
	}
	
	/**
	 * Sets the doc_state of this group.
	 * @param string $doc_state The doc_state
	 */
	function set_doc_state($doc_state)
	{
		$this->set_default_property(self :: PROPERTY_DOC_STATE, $doc_state);
	}
	
	
	/**
	 * Sets the calendar_state of this announcement.
	 * @param int $calendar_state The calendar_state.
	 */
	function set_calendar_state($calendar_state)
	{
		$this->set_default_property(self :: PROPERTY_CALENDAR_STATE, $calendar_state);
	}
	
	/**
	 * Sets the work_state of this announcement.
	 * @param string $work_state The work_state
	 */
	function set_work_state($work_state)
	{
		$this->set_default_property(self :: PROPERTY_WORK_STATE, $work_state);
	}
	
	/**
	 * Sets the announcements_state of this announcement.
	 * @param string $announcements_state The announcements_state
	 */
	function set_announcements_state($announcements_state)
	{
		$this->set_default_property(self :: PROPERTY_ANNOUNCEMENTS_STATE, $announcements_state);
	}
	
	/**
	 * Sets the secret_directory of this announcement.
	 * @param string $secret_directory The secret_directory
	 */
	function set_secret_directory($secret_directory)
	{
		$this->set_default_property(self :: PROPERTY_SECRET_DIRECTORY, $secret_directory);
	}
	
	/**
	 * Sets the self_registration_allowed of this announcement.
	 * @param string $self_registration_allowed The self_registration_allowed
	 */
	function set_self_registration_allowed($self_registration_allowed)
	{
		$this->set_default_property(self :: PROPERTY_SELF_REGISTRATION_ALLOWED, $self_registration_allowed);
	}
	
	/**
	 * Sets the self_unregistration_allowed of this announcement.
	 * @param string $self_unregistration_allowed The self_unregistration_allowed
	 */
	function set_self_unregistration_allowed($self_unregistration_allowed)
	{
		$this->set_default_property(self :: PROPERTY_SELF_UNREGISTRATION_ALLOWED, $self_unregistration_allowed);
	}
	
	function is_valid_group($course)
	{
		if(!$this->get_name() || $this->get_self_registration_allowed() == NULL
			|| $this->get_self_unregistration_allowed() == NULL)
		{
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.group');
			return false;		
		}
		
		return true;
	}
	
	function convert_to_new_group($course)
	{
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');
		
		$lcms_group = new Group();
		$lcms_group->set_course_code($new_course_code);
		$lcms_group->set_name($this->get_name());
		$lcms_group->set_max_number_of_members($this->get_max_student());
		$lcms_group->set_description($this->get_description());
		$lcms_group->set_self_registration_allowed($this->get_self_registration_allowed());
		$lcms_group->set_self_unregistration_allowed($this->get_self_unregistration_allowed());
		$lcms_group->create();
		
		return $lcms_group;
	}
	
	static function get_all_groups($db, $mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_groups($db);
	}
	
}
?>
