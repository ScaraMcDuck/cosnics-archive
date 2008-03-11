<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importgroupreluser.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Group Tutor Relation
 *
 * @author David Van Wayenbergh
 */

class Dokeos185GroupRelUser extends ImportGroupRelUser
{
	private static $mgdm;
	
	/**
	 * group tutor relation properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_GROUP_ID = 'group_id';
	const PROPERTY_STATUS = 'status';
	const PROPERTY_ROLE = 'role';
	
	/**
	 * Default properties of the group tutor relation object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new group tutor relation object.
	 * @param array $defaultProperties The default properties of the group tutor relation
	 *                                 object. Associative array.
	 */
	function Dokeos185GroupRelUser($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this group tutor relation object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this group tutor relation.
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_USER_ID,
					self :: PROPERTY_GROUP_ID, self :: PROPERTY_STATUS, self :: PROPERTY_ROLE);
	}
	
	/**
	 * Sets a default property of this group tutor relation by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Sets the default properties of this link.
	 * @param array $defaultProperties An associative array containing the properties.
	 */
	function set_default_properties($defaultProperties)
	{
		return $this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Returns the id of this group tutor relation.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the user_id of this group tutor relation.
	 * @return String The user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	/**
	 * Returns the group_id of this group tutor relation.
	 * @return String The group_id.
	 */
	function get_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_GROUP_ID);
	}
	
	/**
	 * Returns the status of this group tutor relation.
	 * @return String The status.
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}
	
	/**
	 * Returns the role of this group tutor relation.
	 * @return String The role.
	 */
	function get_role()
	{
		return $this->get_default_property(self :: PROPERTY_ROLE);
	}
	
	/**
	 * Sets the id of this group tutor relation.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the user_id of this group tutor relation.
	 * @param String $user_id The user_id.
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	
	/**
	 * Sets the group_id of this group tutor relation.
	 * @param String $group_id The group_id.
	 */
	function set_group_id($group_id)
	{
		$this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
	}
	
	/**
	 * Sets the status of this group tutor relation.
	 * @param String $status The status.
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}
	
	/**
	 * Sets the role of this group tutor relation.
	 * @param String $role The role.
	 */
	function set_role($role)
	{
		$this->set_default_property(self :: PROPERTY_ROLE, $role);
	}
	
	function is_valid_group_rel_tutor($course)
	{	
		
	}
	
	function convert_to_new_group_rel_tutor($course)
	{	
		
	}
	
	function get_all_group_rel_tutor($db, $mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_group_rel_tutor($db);
	}
}
?>