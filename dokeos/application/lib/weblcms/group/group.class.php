<?php
/**
 * $Id:$
 * @package application.lib.weblcms.group
 * @author Bart Mollet
 */
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
/**
 * This class represents a group of users in a course in the weblcms.
 *
 * To access the values of the properties, this class and its subclasses should
 * provide accessor methods. The names of the properties should be defined as
 * class constants, for standardization purposes. It is recommended that the
 * names of these constants start with the string "PROPERTY_".
 *
 */
class Group
{
	const PROPERTY_ID = 'id';
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_NAME = 'name';
	const PROPERTY_MAX_NUMBER_OF_MEMBERS = 'max_number_of_members';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_SELF_UNREG = 'self_unreg_allowed';
	const PROPERTY_SELF_REG = 'self_reg_allowed';
	/**
	 * The id of this group
	 */
	private $id;
	/**
	 * The code of the course in which this group was created
	 */
	private $course_code;
	/**
	 * The default property values
	 */
	private $defaultProperties;

	/**
	 * Creates a new group object.
	 * @param int $id The numeric ID of the group object. May be omitted if
	 * creating a new object.
	 * @param string $course_code The code of the course in which this group is
	 * created.
	 * @param array $defaultProperties The default properties of the group
	 * object. Associative array.
	 */
	function Group($id = null, $course_code , $defaultProperties = array ())
	{
		$this->id = $id;
		$this->course_code = $course_code;
		$this->defaultProperties = $defaultProperties;
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
	 * Gets the default properties of this group object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Sets a default property of this group object by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	private function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
		return true;
	}
	/**
	 * Get the default properties of all groups.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION,self :: PROPERTY_MAX_NUMBER_OF_MEMBERS, self :: PROPERTY_SELF_REG, self :: PROPERTY_SELF_UNREG);
	}
	/**
	 * Gets the id of this group
	 * @return int
	 */
	function get_id()
	{
		return $this->id;
	}
	/**
	 * Gets the course code of the course in which this group was created
	 * @return string
	 */
	function get_course_code()
	{
		return $this->course_code;
	}
	/**
	 * Gets the name of this group
	 * @return string
	 */
	function get_name()
	{
		return $this->get_default_property(self::PROPERTY_NAME);
	}
	/**
	 * Sets the name of this group
	 * @param string $name
	 */
	function set_name($name)
	{
		return $this->set_default_property(self::PROPERTY_NAME,$name);
	}
	/**
	 * Gets the description of this group
	 * @return string
	 */
	function get_description()
	{
		return $this->get_default_property(self::PROPERTY_DESCRIPTION);
	}
	/**
	 * Sets the description of this group
	 * @param string $description
	 */
	function set_description($description)
	{
		return $this->set_default_property(self::PROPERTY_DESCRIPTION,$description);
	}
	/**
	 * Gets the maximum number of members than can be subscribed to this group
	 * @return int|null If null, no limit is set to the number of members
	 */
	function get_max_number_of_members()
	{
		return $this->get_default_property(self::PROPERTY_MAX_NUMBER_OF_MEMBERS);
	}
	/**
	 * Sets the maximum number of members of this group
	 * If the new value is smaller than the number of members currently
	 * subscribed, no changes are made.
	 * @param int|null $max_number_of_members If null, no limit is set to the
	 * number of members.
	 */
	function set_max_number_of_members($max_number_of_members)
	{
		//Todo: Check current number of members.
		return $this->set_default_property(self::PROPERTY_MAX_NUMBER_OF_MEMBERS,$max_number_of_members);
	}
	/**
	 * Determines if self registration is allowed
	 * @return boolean
	 */
	function is_self_registration_allowed()
	{
		return $this->get_default_property(self::PROPERTY_SELF_REG);
	}
	/**
	 * Sets if self registration is allowed
	 * @param boolean $self_reg
	 */
	function set_self_registration_allowed($self_reg)
	{
		if(is_null($self_reg))
		{
			$self_reg = 0;
		}
		return $this->set_default_property(self::PROPERTY_SELF_REG,$self_reg);
	}
	/**
	 * Determines if self unregistration is allowed
	 * @return boolean
	 */
	function is_self_unregistration_allowed()
	{
		return $this->get_default_property(self::PROPERTY_SELF_UNREG);
	}
	/**
	 * Sets if self unregistration is allowed
	 * @param boolean $self_unreg
	 */
	function set_self_unregistration_allowed($self_unreg)
	{
		if(is_null($self_unreg))
		{
			$self_unreg = 0;
		}
		return $this->set_default_property(self::PROPERTY_SELF_UNREG,$self_unreg);
	}
	/**
	 * Retrieves the users subscribed to this group
	 * @return DatabaseUserResultSet
	 */
	function get_members()
	{
		$wdm = WeblcmsDataManager::get_instance();
		$result = $wdm->retrieve_group_users($this);
		return $result;
	}
	function is_member($user)
	{
		$wdm = WeblcmsDataManager::get_instance();
		return $wdm->is_group_member($this,$user);
	}
	function count_members()
	{
		$members = $this->get_members();
		if(!is_null($members))
		{
			return $members->size();
		}
		return 0;
	}
	/**
	 * Subscribes users to this group
	 * @param array|User A single user or an array of users
	 */
	function subscribe_users($users)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->subscribe_users_to_groups($users,$this);
	}
	/**
	 * Unsubscribes users from this group
	 * @param array|User A single user or an array of users
	 */
	function unsubscribe_users($users)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->unsubscribe_users_from_groups($users,$this);
	}
	/**
	 * Deletes the group object from persistent storage
	 * @return boolean
	 */
	function delete()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->delete_group($this->get_id());
	}

	/**
	 * Creates the group object in persistent storage
	 * @return boolean
	 */
	function create()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->create_group($this);
	}

	/**
	 * Updates the group object in persistent storage
	 * @return boolean
	 */
	function update()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$success = $wdm->update_group($this);
		if (!$success)
		{
			return false;
		}
		return true;
	}
}
?>