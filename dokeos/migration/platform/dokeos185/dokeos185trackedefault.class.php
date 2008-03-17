<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 track_e_default
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEDefault
{
	/**
	 * Dokeos185TrackEDefault properties
	 */
	const PROPERTY_DEFAULT_ID = 'default_id';
	const PROPERTY_DEFAULT_USER_ID = 'default_user_id';
	const PROPERTY_DEFAULT_COURS_CODE = 'default_cours_code';
	const PROPERTY_DEFAULT_DATE = 'default_date';
	const PROPERTY_DEFAULT_EVENT_TYPE = 'default_event_type';
	const PROPERTY_DEFAULT_VALUE_TYPE = 'default_value_type';
	const PROPERTY_DEFAULT_VALUE = 'default_value';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackEDefault object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackEDefault($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_DEFAULT_ID, SELF :: PROPERTY_DEFAULT_USER_ID, SELF :: PROPERTY_DEFAULT_COURS_CODE, SELF :: PROPERTY_DEFAULT_DATE, SELF :: PROPERTY_DEFAULT_EVENT_TYPE, SELF :: PROPERTY_DEFAULT_VALUE_TYPE, SELF :: PROPERTY_DEFAULT_VALUE);
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
	 * Returns the default_id of this Dokeos185TrackEDefault.
	 * @return the default_id.
	 */
	function get_default_id()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_ID);
	}

	/**
	 * Sets the default_id of this Dokeos185TrackEDefault.
	 * @param default_id
	 */
	function set_default_id($default_id)
	{
		$this->set_default_property(self :: PROPERTY_DEFAULT_ID, $default_id);
	}
	/**
	 * Returns the default_user_id of this Dokeos185TrackEDefault.
	 * @return the default_user_id.
	 */
	function get_default_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_USER_ID);
	}

	/**
	 * Sets the default_user_id of this Dokeos185TrackEDefault.
	 * @param default_user_id
	 */
	function set_default_user_id($default_user_id)
	{
		$this->set_default_property(self :: PROPERTY_DEFAULT_USER_ID, $default_user_id);
	}
	/**
	 * Returns the default_cours_code of this Dokeos185TrackEDefault.
	 * @return the default_cours_code.
	 */
	function get_default_cours_code()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_COURS_CODE);
	}

	/**
	 * Sets the default_cours_code of this Dokeos185TrackEDefault.
	 * @param default_cours_code
	 */
	function set_default_cours_code($default_cours_code)
	{
		$this->set_default_property(self :: PROPERTY_DEFAULT_COURS_CODE, $default_cours_code);
	}
	/**
	 * Returns the default_date of this Dokeos185TrackEDefault.
	 * @return the default_date.
	 */
	function get_default_date()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_DATE);
	}

	/**
	 * Sets the default_date of this Dokeos185TrackEDefault.
	 * @param default_date
	 */
	function set_default_date($default_date)
	{
		$this->set_default_property(self :: PROPERTY_DEFAULT_DATE, $default_date);
	}
	/**
	 * Returns the default_event_type of this Dokeos185TrackEDefault.
	 * @return the default_event_type.
	 */
	function get_default_event_type()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_EVENT_TYPE);
	}

	/**
	 * Sets the default_event_type of this Dokeos185TrackEDefault.
	 * @param default_event_type
	 */
	function set_default_event_type($default_event_type)
	{
		$this->set_default_property(self :: PROPERTY_DEFAULT_EVENT_TYPE, $default_event_type);
	}
	/**
	 * Returns the default_value_type of this Dokeos185TrackEDefault.
	 * @return the default_value_type.
	 */
	function get_default_value_type()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_VALUE_TYPE);
	}

	/**
	 * Sets the default_value_type of this Dokeos185TrackEDefault.
	 * @param default_value_type
	 */
	function set_default_value_type($default_value_type)
	{
		$this->set_default_property(self :: PROPERTY_DEFAULT_VALUE_TYPE, $default_value_type);
	}
	/**
	 * Returns the default_value of this Dokeos185TrackEDefault.
	 * @return the default_value.
	 */
	function get_default_value()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_VALUE);
	}

	/**
	 * Sets the default_value of this Dokeos185TrackEDefault.
	 * @param default_value
	 */
	function set_default_value($default_value)
	{
		$this->set_default_property(self :: PROPERTY_DEFAULT_VALUE, $default_value);
	}

}

?>