<?php 
/**
 * webconferencing
 */

/**
 * This class describes a WebconferenceOption data object
 *
 * @author Stefaan Vanbillemont
 */
class WebconferenceOption
{
	const CLASS_NAME = __CLASS__;

	/**
	 * WebconferenceOption properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_CONF_ID = 'conf_id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_VALUE = 'value';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new WebconferenceOption object
	 * @param array $defaultProperties The default properties
	 */
	function WebconferenceOption($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_CONF_ID, self :: PROPERTY_NAME, self :: PROPERTY_VALUE);
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
	 * Returns the id of this WebconferenceOption.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this WebconferenceOption.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the conf_id of this WebconferenceOption.
	 * @return the conf_id.
	 */
	function get_conf_id()
	{
		return $this->get_default_property(self :: PROPERTY_CONF_ID);
	}

	/**
	 * Sets the conf_id of this WebconferenceOption.
	 * @param conf_id
	 */
	function set_conf_id($conf_id)
	{
		$this->set_default_property(self :: PROPERTY_CONF_ID, $conf_id);
	}
	/**
	 * Returns the name of this WebconferenceOption.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this WebconferenceOption.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	/**
	 * Returns the value of this WebconferenceOption.
	 * @return the value.
	 */
	function get_value()
	{
		return $this->get_default_property(self :: PROPERTY_VALUE);
	}

	/**
	 * Sets the value of this WebconferenceOption.
	 * @param value
	 */
	function set_value($value)
	{
		$this->set_default_property(self :: PROPERTY_VALUE, $value);
	}

	function delete()
	{
		$dm = WebconferencingDataManager :: get_instance();
		return $dm->delete_webconference_option($this);
	}

	function create()
	{
		$dm = WebconferencingDataManager :: get_instance();
		$this->set_id($dm->get_next_webconference_option_id());
       	return $dm->create_webconference_option($this);
	}

	function update()
	{
		$dm = WebconferencingDataManager :: get_instance();
		return $dm->update_webconference_option($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>