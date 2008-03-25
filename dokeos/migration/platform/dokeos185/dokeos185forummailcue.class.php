<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 forum_mailcue
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumMailcue
{
	/**
	 * Dokeos185ForumMailcue properties
	 */
	const PROPERTY_THREAD_ID = 'thread_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_POST_ID = 'post_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185ForumMailcue object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185ForumMailcue($defaultProperties = array ())
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
		return array (self :: PROPERTY_THREAD_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_POST_ID);
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
	 * Returns the thread_id of this Dokeos185ForumMailcue.
	 * @return the thread_id.
	 */
	function get_thread_id()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_ID);
	}

	/**
	 * Returns the user_id of this Dokeos185ForumMailcue.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Returns the post_id of this Dokeos185ForumMailcue.
	 * @return the post_id.
	 */
	function get_post_id()
	{
		return $this->get_default_property(self :: PROPERTY_POST_ID);
	}


}

?>