<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcourserelclass.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_rel_class
 *
 * @author David Van Wayenbergh
 */

class dokeos185linkcategory 
{
	/**
	 * link category properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_CATEGORY_TITLE = 'category_title';
	const PROPERTY_DESCRIPTION = 'description';
	
	/**
	 * Alfanumeric identifier of the link category object.
	 */
	private $code;
	
	/**
	 * Default properties of the link category object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new link category object.
	 * @param array $defaultProperties The default properties of the link category
	 *                                 object. Associative array.
	 */
	function Dokeos185LinkCategory($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this link category object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this link category.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all link categories.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_CLASS_CATEGORY_TITLE,
						self :: PROPERTY_CLASS_DESCRIPTION);
	}
	
	/**
	 * Sets a default property of this link category by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default link category
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
	 * Returns the id of this link category.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the category_title of this link category.
	 * @return String The category_title.
	 */
	function get_category_title()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_TITLE);
	}
	
	/**
	 * Returns the description of this link category.
	 * @return String The description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	/**
	 * Sets the id of this link category.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the category_title of this link category.
	 * @param String $category_title The category_title.
	 */
	function set_category_title($category_title)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_TITLE, $category_title);
	}
	
	/**
	 * Sets the description of this link category.
	 * @param String $description The description.
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
}
?>