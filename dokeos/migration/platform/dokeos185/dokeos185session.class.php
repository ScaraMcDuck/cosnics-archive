<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 session
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
	 * Sets the id of this Dokeos185Session.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
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
	 * Sets the id_coach of this Dokeos185Session.
	 * @param id_coach
	 */
	function set_id_coach($id_coach)
	{
		$this->set_default_property(self :: PROPERTY_ID_COACH, $id_coach);
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
	 * Sets the name of this Dokeos185Session.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
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
	 * Sets the nbr_courses of this Dokeos185Session.
	 * @param nbr_courses
	 */
	function set_nbr_courses($nbr_courses)
	{
		$this->set_default_property(self :: PROPERTY_NBR_COURSES, $nbr_courses);
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
	 * Sets the nbr_users of this Dokeos185Session.
	 * @param nbr_users
	 */
	function set_nbr_users($nbr_users)
	{
		$this->set_default_property(self :: PROPERTY_NBR_USERS, $nbr_users);
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
	 * Sets the nbr_classes of this Dokeos185Session.
	 * @param nbr_classes
	 */
	function set_nbr_classes($nbr_classes)
	{
		$this->set_default_property(self :: PROPERTY_NBR_CLASSES, $nbr_classes);
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
	 * Sets the date_start of this Dokeos185Session.
	 * @param date_start
	 */
	function set_date_start($date_start)
	{
		$this->set_default_property(self :: PROPERTY_DATE_START, $date_start);
	}
	/**
	 * Returns the date_end of this Dokeos185Session.
	 * @return the date_end.
	 */
	function get_date_end()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_END);
	}

	/**
	 * Sets the date_end of this Dokeos185Session.
	 * @param date_end
	 */
	function set_date_end($date_end)
	{
		$this->set_default_property(self :: PROPERTY_DATE_END, $date_end);
	}

}

?>