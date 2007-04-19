<?php
/**
 * @package users
 */
//require_once dirname(__FILE__).'/accessiblelearningobject.class.php';
//require_once dirname(__FILE__).'/repositorydatamanager.class.php';
//require_once dirname(__FILE__).'/repositoryutilities.class.php';
//require_once dirname(__FILE__).'/condition/equalitycondition.class.php';
//require_once dirname(__FILE__).'/learningobjectdifference.class.php';
//require_once dirname(__FILE__).'/learningobjectdisplay.class.php';
/**
 *	This class represents the different quota values for a user. (for each learning object type)
 *
 *	User objects have a number of default properties:
 *	- user_id: the user_id;
 *	- learning object type: the learning object type;
 *	- user_quota: the user quota:
 *
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */

class Userquota
{
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_LEARNING_OBJECT_TYPE = 'learning_object_type';
	const PROPERTY_USER_QUOTA = 'user_quota';
	

	/**
	 * Numeric identifier of the userquota object.
	 */
	private $user_id;

	/**
	 * Default properties of the user object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new user object.
	 * @param int $id The numeric ID of the user object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the user
	 *                                 object. Associative array.
	 */
	function Userquota($user_id = 0, $defaultProperties = array ())
	{
		$this->user_id = $user_id;
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this user object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this user.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all users.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_USER_ID, self :: PROPERTY_LEARNING_OBJECT_TYPE, self :: PROPERTY_USER_QUOTA);
	}
		
	/**
	 * Sets a default property of this user by name.
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

	/**
	 * Returns the user_id of this user.
	 * @return int The user_id.
	 */
	function get_user_id()
	{
		return $this->user_id;
	}
	
	/**
	 * Returns the learning object type.
	 * @return String The lastname
	 */
	function get_learning_object_type()
	{
		return $this->get_default_property(self :: PROPERTY_LEARNING_OBJECT_TYPE);
	}
	
	/**
	 * Returns the user quota.
	 * @return String The firstname
	 */
	function get_user_quota()
	{
		return $this->get_default_property(self :: PROPERTY_USER_QUOTA);
	}
	
	/**
	 * Sets the user_id of this user.
	 * @param int $user_id The user_id.
	 */
	function set_user_id($user_id)
	{
		$this->user_id = $user_id;
	}
	
	/**
	 * Sets the learning object type.
	 * @param $type the learning object type.
	 */
	function set_learning_object_type($type)
	{
		$this->set_default_property(self :: PROPERTY_LEARNING_OBJECT_TYPE, $type);
	}
	
	/**
	 * Sets the user quota.
	 * @param $quota the quota
	 */
	function set_user_quota($quota)
	{
		$this->set_default_property(self :: PROPERTY_USER_QUOTA, $quota);
	}
	
	function update()
	{
		$udm = UsersDataManager :: get_instance();
		return $udm->update_user_quota($this);
	}
}
?>