<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcourse.class.php';
require_once dirname(__FILE__).'/../../../application/lib/weblcms/course/course.class.php';

/**
 * This class represents an old Dokeos 1.8.5 forum category
 *
 * @author David Van Wayenbergh
 */
 
 
class dokeos185forumcategory
{

    /**
	 * Migration data manager
	 */
	private static $mgdm;
	
	/**
	 * forum forum properties
	 */
	const PROPERTY_CAT_ID = 'cat_id';
	const PROPERTY_CAT_TITLE = 'cat_title';
	const PROPERTY_CAT_COMMENT = 'cat_comment';
	const PROPERTY_LOCKED = 'locked';
	const PROPERTY_CAT_ORDER = 'forum_id';
		
	/**
	 * Default properties of the forum category object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new forum category object.
	 * @param array $defaultProperties The default properties of the forum category
	 *                                 object. Associative array.
	 */
	function Dokeos185ForumCategory($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this forum category object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this forum category.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all forum categories.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_CAT_ID,self::PROPERTY_CAT_TITLE,self::PROPERTY_CAT_COMMENT,
		self::PROPERTY_LOCKED,self::PROPERTY_CAT_ORDER);
	}
	
	/**
	 * Sets a default property of this forum category by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Sets the default properties of this forum category
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Checks if the given identifier is the name of a default forum category
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
	 * Returns the cat_id of this forum category.
	 * @return int The cat_id.
	 */
	function get_cat_id()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_ID);
	}
	
	/**
	 * Returns the cat_title of this forum category.
	 * @return int The cat_title.
	 */
	function get_cat_title()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_TITLE);
	}
	
	/**
	 * Returns the cat_comment of this forum category.
	 * @return int The cat_comment.
	 */
	function get_cat_comment()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_COMMENT);
	}
	
	/**
	 * Returns the locked of this forum category.
	 * @return int The locked.
	 */
	function get_locked()
	{
		return $this->get_default_property(self :: PROPERTY_LOCKED);
	}
	
	/**
	 * Returns the cat_order of this forum category.
	 * @return int The cat_order.
	 */
	function get_cat_order()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_ORDER);
	}
}
?>