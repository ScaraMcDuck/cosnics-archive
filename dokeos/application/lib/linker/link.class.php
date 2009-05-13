<?php

require_once dirname(__FILE__).'/linker_data_manager.class.php';

/**
 * @package link
 */
/**
 *  @author Sven Vanpoucke
 */

class Link
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_URL = 'url';
	
	/**
	 * Default properties of the link object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new link object.
	 * @param int $id The numeric ID of the link object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the link
	 *                                 object. Associative array.
	 */
	function Link($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this link object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this link.
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
	 * Get the default properties of all links.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_URL);
	}
		
	/**
	 * Sets a default property of this link by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default link
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
	 * Returns the id of this link.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the name of this link.
	 * @return String The name
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	/**
	 * Returns the description of this link.
	 * @return String The description
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	/**
	 * Returns the url of this link.
	 * @return String The url
	 */
	function get_url()
	{
		return $this->get_default_property(self :: PROPERTY_URL);
	}
	
	/**
	 * Sets the link_id of this link.
	 * @param int $link_id The link_id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}		
	
	/**
	 * Sets the name of this link.
	 * @param String $name the name.
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	/**
	 * Sets the description of this link.
	 * @param String $description the description.
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	/**
	 * Sets the url of this link.
	 * @param String $url the url.
	 */
	function set_url($url)
	{
		$this->set_default_property(self :: PROPERTY_URL, $url);
	}
	
	function delete()
	{
		$ldm = LinkerDataManager :: get_instance();
		return $ldm->delete_link($this);
	}
	
	function create()
	{
		$ldm = LinkerDataManager :: get_instance();
		$this->set_id($ldm->get_next_link_id());
       	return $ldm->create_link($this);
	}
	
	function update() 
	{
		$ldm = LinkerDataManager :: get_instance();
		return $ldm->update_link($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

}
?>