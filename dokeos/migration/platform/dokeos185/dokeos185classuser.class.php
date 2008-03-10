<?php
/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importclass.class.php';
require_once dirname(__FILE__).'/../../../classgroup/lib/classgroupreluser.class.php';

/**
 * This class represents an old Dokeos 1.8.5 class
 *
 * @author David Van Wayenbergh
 * @author Sven Vanpoucke
 */
 
class Dokeos185ClassUser extends Import
{
	/**
	 * Migration data manager
	 */
	private static $mgdm;

	/**
	 * class relation user properties
	 */
	 
	const PROPERTY_CLASS_ID = 'class_id';
	const PROPERTY_USER_ID = 'user_id';
	
	/**
	 * Alfanumeric identifier of the class_user object.
	 */
	private $code;
	
	/**
	 * Default properties of the class_user object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new class_user object.
	 * @param array $defaultProperties The default properties of the class_user
	 *                                 object. Associative array.
	 */
	function Dokeos185ClassUser($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this class_user object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this class_user.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all classe_users.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self::PROPERTY_CLASS_ID, self :: PROPERTY_USER_ID);
	}
	
	/**
	 * Sets a default property of this class_user by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default class_user
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
	 * Returns the class_id of this class_user.
	 * @return int The id.
	 */
	function get_class_id()
	{
		return $this->get_default_property(self :: PROPERTY_CLASS_ID);
	}
	 
	/**
	 * Returns the code of this class_user.
	 * @return int The code.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	/**
	 * Sets the class_id of this class_user.
	 * @param int $class_id The class_id.
	 */
	function set_class_id($class_id)
	{
		$this->set_default_property(self :: PROPERTY_CLASS_ID, $class_id);
	}
	
	/**
	 * Sets the user_id of this class_user.
	 * @param int $user_id The user_id.
	 */
	function set_code($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	
	function is_valid_class_user()
	{
		if(!$this->get_class_id() || !$this->get_user_id() || 
			self :: $mgdm->get_failed_element('dokeos_main.class', $this->get_class_id()) ||
			self :: $mgdm->get_failed_element('dokeos_main.user', $this->get_user_id()))
		{
			self :: $mgdm->add_failed_element($this->get_class_id() . '-' . $this->get_user_id(),
				'dokeos_main.class_user');
			return false;
		}
		
		return true;
	}
	
	function convert_to_new_class_user()
	{
		$lcms_class_user = new ClassGroupRelUser();
		
		$class_id = self :: $mgdm->get_id_reference($this->get_class_id(), 'classgroup_classgroup');
		if($class_id)
			$lcms_class_user->set_classgroup_id($class_id);
		
		$user_id = self :: $mgdm->get_id_reference($this->get_user_id(), 'user_user');
		if($user_id)
			$lcms_class_user->set_user_id($user_id);
		
		$lcms_class_user->create();
		
		return $lcms_class_user;
	}
	
	function get_all_class_users($mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_class_users();
	}
}
?>
