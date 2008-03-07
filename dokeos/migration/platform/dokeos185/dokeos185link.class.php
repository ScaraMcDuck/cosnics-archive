<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importlink.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/learning_object/link/link.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_rel_class
 *
 * @author David Van Wayenbergh
 */

class Dokeos185Link extends Import
{
	
 	/**
	 * course relation class properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_URL = 'url';
	const PROPERTY_TITLE = 'title';
 	const PROPERTY_DESCRIPTION = 'description';
 	const PROPERTY_CATEGORY_ID = 'category_id';
 	const PROPERTY_DISPLAY_ORDER = 'display_order';
 	const PROPERTY_ON_HOMEPAGE = 'on_homepage';
 	
 	/**
	 * Alfanumeric identifier of the link object.
	 */
	private $code;
	
	/**
	 * Default properties of the link object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new link object.
	 * @param array $defaultProperties The default properties of the link
	 *                                 object. Associative array.
	 */
	function Dokeos185Link($defaultProperties = array ())
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
	
	/**
	 * Get the default properties of all links.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID,self :: PROPERTY_URL,self :: PROPERTY_TITLE,
			self :: PROPERTY_DESCRIPTION,self :: PROPERTY_CATEGORY_ID, self :: PROPERTY_DISPLAY_ORDER,
			self :: PROPERTY_ON_HOMEPAGE);
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
	 * Checks if the given identifier is the name of a default course
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
	 * Returns the url of this link.
	 * @return String The url.
	 */
	function get_url()
	{
		return $this->get_default_property(self :: PROPERTY_URL);
	}
	
	/**
	 * Returns the title of this link.
	 * @return String The title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	/**
	 * Returns the description of this link.
	 * @return String The description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	/**
	 * Returns the category_id of this link.
	 * @return int The category_id.
	 */
	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
	}
	
	/**
	 * Returns the display_order of this link.
	 * @return int The display_order.
	 */
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}
	
	/**
	 * Returns the on_homepage of this link.
	 * @return String The on_homepage.
	 */
	function get_on_homepage()
	{
		return $this->get_default_property(self :: PROPERTY_ON_HOMEPAGE);
	}
	
	/**
	 * Sets the id of this link.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the url of this link.
	 * @param String $url The url.
	 */
	function set_url($url)
	{
		$this->set_default_property(self :: PROPERTY_URL, $url);
	}
	
	/**
	 * Sets the title of this link.
	 * @param String $title The title.
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	
	/**
	 * Sets the description of this link.
	 * @param String $description The description.
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	/**
	 * Sets the category_category_id of this link.
	 * @param int $category_id The category_id.
	 */
	function set_category_id($category_id)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
	}
	
	/**
	 * Sets the display_order of this link.
	 * @param int $display_order The display_order.
	 */
	function set_display_order($display_order)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
	}
	
	/**
	 * Sets the on_homepage of this link.
	 * @param int $on_homepage The on_homepage.
	 */
	function set_on_homepage($on_homepage)
	{
		$this->set_default_property(self :: PROPERTY_ON_HOMEPAGE, $on_homepage);
	}
	
	
}
?>
