<?php
require_once dirname(__FILE__).'/webservice_data_manager.class.php';

/**
 * @package webservice
 */
/**
 *	@author Stefan Billiet
 */

class WebserviceCategory
{
	const CLASS_NAME = __CLASS__;	
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_PARENT = 'parent';
	
/**
	 * Default properties of the webservice_category object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new webservice_category object.
	 * @param int $id The numeric ID of the webservice_category object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the webservice_category
	 *                                 object. Associative array.
	 */
	function WebserviceCategory($id = 0, $defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property of this webservice_category object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties of this webservice_category.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Get the default properties of all webservice_categories.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME);
	}
	
	/**
	 * Sets a default property of this webservice_category by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default webservice_category
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
	 * Returns the id of this webservice_category.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the name of this webservice_category.
	 * @return String The name
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	function get_parent()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT);
	}
	
	/**
	 * Sets the webservice_id of this webservice.
	 * @param int $webservice_id The webservice_id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}		
	
	/**
	 * Sets the name of this webservice.
	 * @param String $name the name.
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	function set_parent($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $parent);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function create()
	{
		$wdm = WebserviceDataManager :: get_instance();
		$this->set_id($wdm->get_next_webservice_id());
		return $wdm->create_webservice_category($this);
	}
}
?>