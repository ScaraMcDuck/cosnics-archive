<?php
/**
 * @package users
 */
/**
 *	@author Sven Vanpoucke
 */

class BuddyListItem
{
	const CLASS_NAME					= __CLASS__;
	
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_BUDDY_ID = 'buddy_id';
	const PROPERTY_CATEGORY_ID = 'category_id';
	const PROPERTY_STATUS = 'status';

	/**
	 * Default properties of the userrole object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	function BuddyListItem($defaultProperties = array ())
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
		return array (self :: PROPERTY_USER_ID, self :: PROPERTY_BUDDY_ID, self :: PROPERTY_CATEGORY_ID, self :: PROPERTY_STATUS);
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
	 * Checks if the given user_identifier is the name of a default user
	 * property.
	 * @param string $name The user_identifier.
	 * @return boolean True if the user_identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}

	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	
	function get_buddy_id()
	{
		return $this->get_default_property(self :: PROPERTY_BUDDY_ID);
	}
	
	function set_buddy_id($buddy_id)
	{
		$this->set_default_property(self :: PROPERTY_BUDDY_ID, $buddy_id);
	}

	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
	}
	
	function set_category_id($category_id)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
	}
	
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}
	
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}
	
	function delete()
	{
		return UserDataManager :: get_instance()->delete_buddy_list_item($this);
	}
	
	function create()
	{
		$udm = UsersDataManager :: get_instance();
		return $udm->create_buddy_list_item($this);
	}
	
	function update() 
	{
		$udm = UsersDataManager :: get_instance();
		return $udm->update_buddy_list_item($this);	
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