<?php
/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcourse.class.php';
require_once dirname(__FILE__).'/../../../application/lib/weblcms/course/course.class.php';

/**
 * This class represents an old Dokeos 1.8.5 forum mailcue
 *
 * @author David Van Wayenbergh
 */
 
 
class dokeos185forummailcue 
{

    /**
	 * Migration data manager
	 */
	private static $mgdm;
	
	/**
	 * forum forum properties
	 */
	const PROPERTY_THREAD_ID = 'thread_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_POST_ID = 'post_id';
	
	/**
	 * Default properties of the forum mailcue object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new forum mailcue object.
	 * @param array $defaultProperties The default properties of the forum mailcue
	 *                                 object. Associative array.
	 */
	function Dokeos185ForumMailCue($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this forum mailcue object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this forum mailcue.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all forum mailcues.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_THREAD_ID,self::PROPERTY_USER_ID,self::PROPERTY_POST_ID);
	}
	
	/**
	 * Sets a default property of this forum mailcue by name.
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
	 * Checks if the given identifier is the name of a default forum mailcue
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
	 * Returns the thread_id of this forum mailcue.
	 * @return int The thread_id.
	 */
	function get_thread_id()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_ID);
	}
	
	/**
	 * Returns the user_id of this forum mailcue.
	 * @return int The user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	/**
	 * Returns the post_id of this forum mailcue.
	 * @return int The post_id.
	 */
	function get_post_id()
	{
		return $this->get_default_property(self :: PROPERTY_POST_ID);
	}
}
?>
