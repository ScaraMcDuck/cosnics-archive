<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 group_info
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GroupInfo
{
	/**
	 * Dokeos185GroupInfo properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_CATEGORY_ID = 'category_id';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_MAX_STUDENT = 'max_student';
	const PROPERTY_DOC_STATE = 'doc_state';
	const PROPERTY_CALENDAR_STATE = 'calendar_state';
	const PROPERTY_WORK_STATE = 'work_state';
	const PROPERTY_ANNOUNCEMENTS_STATE = 'announcements_state';
	const PROPERTY_SECRET_DIRECTORY = 'secret_directory';
	const PROPERTY_SELF_REGISTRATION_ALLOWED = 'self_registration_allowed';
	const PROPERTY_SELF_UNREGISTRATION_ALLOWED = 'self_unregistration_allowed';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185GroupInfo object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185GroupInfo($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_NAME, SELF :: PROPERTY_CATEGORY_ID, SELF :: PROPERTY_DESCRIPTION, SELF :: PROPERTY_MAX_STUDENT, SELF :: PROPERTY_DOC_STATE, SELF :: PROPERTY_CALENDAR_STATE, SELF :: PROPERTY_WORK_STATE, SELF :: PROPERTY_ANNOUNCEMENTS_STATE, SELF :: PROPERTY_SECRET_DIRECTORY, SELF :: PROPERTY_SELF_REGISTRATION_ALLOWED, SELF :: PROPERTY_SELF_UNREGISTRATION_ALLOWED);
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
	 * Returns the id of this Dokeos185GroupInfo.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185GroupInfo.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the name of this Dokeos185GroupInfo.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this Dokeos185GroupInfo.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	/**
	 * Returns the category_id of this Dokeos185GroupInfo.
	 * @return the category_id.
	 */
	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
	}

	/**
	 * Sets the category_id of this Dokeos185GroupInfo.
	 * @param category_id
	 */
	function set_category_id($category_id)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
	}
	/**
	 * Returns the description of this Dokeos185GroupInfo.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Sets the description of this Dokeos185GroupInfo.
	 * @param description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	/**
	 * Returns the max_student of this Dokeos185GroupInfo.
	 * @return the max_student.
	 */
	function get_max_student()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_STUDENT);
	}

	/**
	 * Sets the max_student of this Dokeos185GroupInfo.
	 * @param max_student
	 */
	function set_max_student($max_student)
	{
		$this->set_default_property(self :: PROPERTY_MAX_STUDENT, $max_student);
	}
	/**
	 * Returns the doc_state of this Dokeos185GroupInfo.
	 * @return the doc_state.
	 */
	function get_doc_state()
	{
		return $this->get_default_property(self :: PROPERTY_DOC_STATE);
	}

	/**
	 * Sets the doc_state of this Dokeos185GroupInfo.
	 * @param doc_state
	 */
	function set_doc_state($doc_state)
	{
		$this->set_default_property(self :: PROPERTY_DOC_STATE, $doc_state);
	}
	/**
	 * Returns the calendar_state of this Dokeos185GroupInfo.
	 * @return the calendar_state.
	 */
	function get_calendar_state()
	{
		return $this->get_default_property(self :: PROPERTY_CALENDAR_STATE);
	}

	/**
	 * Sets the calendar_state of this Dokeos185GroupInfo.
	 * @param calendar_state
	 */
	function set_calendar_state($calendar_state)
	{
		$this->set_default_property(self :: PROPERTY_CALENDAR_STATE, $calendar_state);
	}
	/**
	 * Returns the work_state of this Dokeos185GroupInfo.
	 * @return the work_state.
	 */
	function get_work_state()
	{
		return $this->get_default_property(self :: PROPERTY_WORK_STATE);
	}

	/**
	 * Sets the work_state of this Dokeos185GroupInfo.
	 * @param work_state
	 */
	function set_work_state($work_state)
	{
		$this->set_default_property(self :: PROPERTY_WORK_STATE, $work_state);
	}
	/**
	 * Returns the announcements_state of this Dokeos185GroupInfo.
	 * @return the announcements_state.
	 */
	function get_announcements_state()
	{
		return $this->get_default_property(self :: PROPERTY_ANNOUNCEMENTS_STATE);
	}

	/**
	 * Sets the announcements_state of this Dokeos185GroupInfo.
	 * @param announcements_state
	 */
	function set_announcements_state($announcements_state)
	{
		$this->set_default_property(self :: PROPERTY_ANNOUNCEMENTS_STATE, $announcements_state);
	}
	/**
	 * Returns the secret_directory of this Dokeos185GroupInfo.
	 * @return the secret_directory.
	 */
	function get_secret_directory()
	{
		return $this->get_default_property(self :: PROPERTY_SECRET_DIRECTORY);
	}

	/**
	 * Sets the secret_directory of this Dokeos185GroupInfo.
	 * @param secret_directory
	 */
	function set_secret_directory($secret_directory)
	{
		$this->set_default_property(self :: PROPERTY_SECRET_DIRECTORY, $secret_directory);
	}
	/**
	 * Returns the self_registration_allowed of this Dokeos185GroupInfo.
	 * @return the self_registration_allowed.
	 */
	function get_self_registration_allowed()
	{
		return $this->get_default_property(self :: PROPERTY_SELF_REGISTRATION_ALLOWED);
	}

	/**
	 * Sets the self_registration_allowed of this Dokeos185GroupInfo.
	 * @param self_registration_allowed
	 */
	function set_self_registration_allowed($self_registration_allowed)
	{
		$this->set_default_property(self :: PROPERTY_SELF_REGISTRATION_ALLOWED, $self_registration_allowed);
	}
	/**
	 * Returns the self_unregistration_allowed of this Dokeos185GroupInfo.
	 * @return the self_unregistration_allowed.
	 */
	function get_self_unregistration_allowed()
	{
		return $this->get_default_property(self :: PROPERTY_SELF_UNREGISTRATION_ALLOWED);
	}

	/**
	 * Sets the self_unregistration_allowed of this Dokeos185GroupInfo.
	 * @param self_unregistration_allowed
	 */
	function set_self_unregistration_allowed($self_unregistration_allowed)
	{
		$this->set_default_property(self :: PROPERTY_SELF_UNREGISTRATION_ALLOWED, $self_unregistration_allowed);
	}

}

?>