<?php

require_once dirname(__FILE__).'/class_group_data_manager.class.php';
/**
 * @package users
 */
/**
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */

class ClassGroupRelUser
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_CLASSGROUP_ID = 'classgroup_id';
	const PROPERTY_USER_ID = 'user_id';

	private $defaultProperties;

	function ClassGroupRelUser($classgroup_id = 0, $user_id = 0, $defaultProperties = array())
	{
		$this->set_classgroup_id($classgroup_id);
		$this->set_user_id($user_id);
	}
	
	function get_classgroup_id()
	{
		return $this->get_default_property(self :: PROPERTY_CLASSGROUP_ID);
	}
	
	function set_classgroup_id($classgroup_id)
	{
		$this->set_default_property(self :: PROPERTY_CLASSGROUP_ID, $classgroup_id);
	}
	
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}	
	
		
	/**
	 * Gets a default property of this group object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this group.
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
	 * Get the default properties of all groups.
	 * @return array The property names.
	 */
	function get_default_property_names()
	{
		return array (self :: PROPERTY_CLASSGROUP_ID, self :: PROPERTY_USER_ID);
	}
		
	/**
	 * Sets a default property of this group by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Instructs the Datamanager to delete this user.
	 * @return boolean True if success, false otherwise.
	 */
	function delete()
	{
		return ClassGroupDataManager :: get_instance()->delete_classgroup_rel_user($this);
	}
	
	function create()
	{
		$gdm = ClassGroupDataManager :: get_instance();
		return $gdm->create_classgroup_rel_user($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>