<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 session
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Session
{
	/**
	 * Dokeos185Session properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_ID_COACH = 'id_coach';
	const PROPERTY_NAME = 'name';
	const PROPERTY_NBR_COURSES = 'nbr_courses';
	const PROPERTY_NBR_USERS = 'nbr_users';
	const PROPERTY_NBR_CLASSES = 'nbr_classes';
	const PROPERTY_DATE_START = 'date_start';
	const PROPERTY_DATE_END = 'date_end';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185Session object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Session($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_ID_COACH, SELF :: PROPERTY_NAME, SELF :: PROPERTY_NBR_COURSES, SELF :: PROPERTY_NBR_USERS, SELF :: PROPERTY_NBR_CLASSES, SELF :: PROPERTY_DATE_START, SELF :: PROPERTY_DATE_END);
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
	 * Returns the id of this Dokeos185Session.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the id_coach of this Dokeos185Session.
	 * @return the id_coach.
	 */
	function get_id_coach()
	{
		return $this->get_default_property(self :: PROPERTY_ID_COACH);
	}

	/**
	 * Returns the name of this Dokeos185Session.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Returns the nbr_courses of this Dokeos185Session.
	 * @return the nbr_courses.
	 */
	function get_nbr_courses()
	{
		return $this->get_default_property(self :: PROPERTY_NBR_COURSES);
	}

	/**
	 * Returns the nbr_users of this Dokeos185Session.
	 * @return the nbr_users.
	 */
	function get_nbr_users()
	{
		return $this->get_default_property(self :: PROPERTY_NBR_USERS);
	}

	/**
	 * Returns the nbr_classes of this Dokeos185Session.
	 * @return the nbr_classes.
	 */
	function get_nbr_classes()
	{
		return $this->get_default_property(self :: PROPERTY_NBR_CLASSES);
	}

	/**
	 * Returns the date_start of this Dokeos185Session.
	 * @return the date_start.
	 */
	function get_date_start()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_START);
	}

	/**
	 * Returns the date_end of this Dokeos185Session.
	 * @return the date_end.
	 */
	function get_date_end()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_END);
	}


}

?>