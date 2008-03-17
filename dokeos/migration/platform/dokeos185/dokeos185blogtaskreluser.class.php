<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 blog_task_rel_user
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogTaskRelUser
{
	/**
	 * Dokeos185BlogTaskRelUser properties
	 */
	const PROPERTY_BLOG_ID = 'blog_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_TASK_ID = 'task_id';
	const PROPERTY_TARGET_DATE = 'target_date';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185BlogTaskRelUser object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185BlogTaskRelUser($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_BLOG_ID, SELF :: PROPERTY_USER_ID, SELF :: PROPERTY_TASK_ID, SELF :: PROPERTY_TARGET_DATE);
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
	 * Returns the blog_id of this Dokeos185BlogTaskRelUser.
	 * @return the blog_id.
	 */
	function get_blog_id()
	{
		return $this->get_default_property(self :: PROPERTY_BLOG_ID);
	}

	/**
	 * Sets the blog_id of this Dokeos185BlogTaskRelUser.
	 * @param blog_id
	 */
	function set_blog_id($blog_id)
	{
		$this->set_default_property(self :: PROPERTY_BLOG_ID, $blog_id);
	}
	/**
	 * Returns the user_id of this Dokeos185BlogTaskRelUser.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Dokeos185BlogTaskRelUser.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	/**
	 * Returns the task_id of this Dokeos185BlogTaskRelUser.
	 * @return the task_id.
	 */
	function get_task_id()
	{
		return $this->get_default_property(self :: PROPERTY_TASK_ID);
	}

	/**
	 * Sets the task_id of this Dokeos185BlogTaskRelUser.
	 * @param task_id
	 */
	function set_task_id($task_id)
	{
		$this->set_default_property(self :: PROPERTY_TASK_ID, $task_id);
	}
	/**
	 * Returns the target_date of this Dokeos185BlogTaskRelUser.
	 * @return the target_date.
	 */
	function get_target_date()
	{
		return $this->get_default_property(self :: PROPERTY_TARGET_DATE);
	}

	/**
	 * Sets the target_date of this Dokeos185BlogTaskRelUser.
	 * @param target_date
	 */
	function set_target_date($target_date)
	{
		$this->set_default_property(self :: PROPERTY_TARGET_DATE, $target_date);
	}

}

?>