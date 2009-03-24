<?php
/**
 * @package users
 */
/**
 *	@author Sven Vanpoucke
 */

class BuddyListCategory
{
	const CLASS_NAME					= __CLASS__;
	
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_USER_ID = 'user_id';

	/**
	 * Default properties of the userrole object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	function BuddyListCategory($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this user quota object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this user quota object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all users quota objects.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_USER_ID);
	}
		
	/**
	 * Sets a default property of this user quota by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default user
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}

	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}	
	
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}	
	
	function delete()
	{
		return UserDataManager :: get_instance()->delete_buddy_list_category($this);
	}
	
	function create()
	{
		$udm = UserDataManager :: get_instance();
		$this->set_id($udm->get_next_buddy_list_category_id());
		return $udm->create_buddy_list_category($this);
	}
	
	function update() 
	{
		$udm = UserDataManager :: get_instance();
		return $udm->update_buddy_list_category($this);	
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
}
?>