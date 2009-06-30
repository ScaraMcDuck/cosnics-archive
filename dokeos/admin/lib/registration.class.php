<?php
/**
 * @package admin.lib
 * @author Hans De Bisschop
 */

require_once dirname(__FILE__).'/admin_data_manager.class.php';

class Registration
{
	const CLASS_NAME				= __CLASS__;

	const PROPERTY_ID		= 'id';
	const PROPERTY_TYPE		= 'type';
	const PROPERTY_NAME		= 'name';
	const PROPERTY_STATUS	= 'status';
	const PROPERTY_VERSION	= 'version';

	const TYPE_LEARNING_OBJECT = 'learning_object';
	const TYPE_APPLICATION = 'application';

	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;

	private $id;
	private $defaultProperties;

	/**
	 * Creates a new registration.
	 * @param int $id The numeric ID of the registration. May be omitted
	 *                if creating a new registration.
	 * @param array $defaultProperties The default properties of the registration. Associative array.
	 */
	function Registration($id = 0, $defaultProperties = array ())
	{
		$this->set_id($id);
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property of this registration by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties of this registration.
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
	 * Get the default properties of registrations.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_TYPE, self :: PROPERTY_NAME, self :: PROPERTY_STATUS, self :: PROPERTY_VERSION);
	}

	/**
	 * Sets a default property of this registration by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Checks if the given identifier is the name of a default registration
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
	 * Returns the id of this registration.
	 * @return int The registration id
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the type of this registration.
	 * @return int The type
	 */
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}

	/**
	 * Returns the name of this registration.
	 * @return int the name
	 */
	function get_name()
	{
	 	return $this->get_default_property(self :: PROPERTY_NAME);
	}

	 /**
	  * Returns the status of this registration.
	  * @return int the status
	  */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}

	 /**
	  * Returns the version of the registered item.
	  * @return String the version
	  */
	function get_version()
	{
		return $this->get_default_property(self :: PROPERTY_VERSION);
	}

	/**
	 * Sets the id of this registration.
	 * @param int $pm_id The registration id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Sets the type of this registration.
	 * @param Int $id the registration type.
	 */
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_TYPE, $type);
	}

	/**
	 * Sets the name of this registration.
	 * @param int $name the name.
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Sets the status of this registration.
	 * @param int $status the status.
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}

	/**
	 * Sets the version of this registered item.
	 * @param String $version the version.
	 */
	function set_version($version)
	{
		$this->set_default_property(self :: PROPERTY_VERSION, $version);
	}

	function is_active()
	{
		return $this->get_status();
	}

	function toggle_status()
	{
	    $this->set_status(!$this->get_status());
	}

	/**
	 * Instructs the data manager to create the registration, making it
	 * persistent. Also assigns a unique ID to the registration.
	 * @return boolean True if creation succeeded, false otherwise.
	 */
	function create()
	{
		$adm = AdminDataManager :: get_instance();
		$id = $adm->get_next_registration_id();
		$this->set_id($id);
		return $adm->create_registration($this);
	}

	/**
	 * Deletes this registration from persistent storage
	 * @see AdminDataManager :: delete_registration()
	 */
	function delete()
	{
		return AdminDataManager :: get_instance()->delete_registration($this);
	}

	/**
	 * Updates this registration in persistent storage
	 * @see AdminDataManager :: update_registration()
	 */
	function update()
	{
		return AdminDataManager :: get_instance()->update_registration($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>
