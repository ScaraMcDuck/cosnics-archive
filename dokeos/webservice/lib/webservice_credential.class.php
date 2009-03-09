<?php
require_once dirname(__FILE__).'/webservice_data_manager.class.php';

/**
 * @package webservice
 */
/**
 *	@author Stefan Billiet
 */

class WebserviceCredential
{
	const CLASS_NAME = __CLASS__;	
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_HASH = 'hash';
	const PROPERTY_IP = 'ip';
	const PROPERTY_TIME_CREATED = 'time_created';
	
	/**
	 * Default properties of the webservice_category object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new webservice_credential object.
	 * @param array $defaultProperties The default properties of the webservice_category
	 *                                 object. Associative array.
	 */
	function WebserviceCredential($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property of this webservice_credential object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties of this webservice_credential.
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
	 * Get the default properties of all webservice_credentials.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_USER_ID, self :: PROPERTY_HASH, self :: PROPERTY_IP, self :: PROPERTY_TIME_CREATED, self :: PROPERTY_COMPLETED);
	}
	
	/**
	 * Sets a default property of this webservice_credential by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default webservice_credential
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
	 * Returns the id of this webservice_credential.
	 * @return int The id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Returns the name of this webservice_credential.
	 * @return String The name
	 */
	function get_hash()
	{
		return $this->get_default_property(self :: PROPERTY_HASH);
	}
	/**
	 * Returns the logged IP-address of this webservice_credential.
	 * @return String IP
	 */
	function get_ip()
	{
		return $this->get_default_property(self :: PROPERTY_IP);
	}
	
	/**
	 * Returns the time this webservice_credential was created.
	 * @return int
	 */
	function get_time_created()
	{
		return $this->get_default_property(self :: PROPERTY_TIME_CREATED);
	}

	
	/**
	 * Sets the user_id of this credential.
	 * @param int $webservice_id The webservice_id.
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}		
	
	/**
	 * Sets the hash of this webservice_credential.
	 * @param String $hash the hash.
	 */
	function set_hash($hash)
	{
		$this->set_default_property(self :: PROPERTY_HASH, $hash);
	}
	/**
	 * Sets the logged ip of this webservice_credential.
	 * @param String $ip the ip.
	 */
	function set_ip($ip)
	{
		$this->set_default_property(self :: PROPERTY_IP, $ip);
	}
	/**
	 * Sets the time this webservice_credential was created.
	 * @param String $time_created the time_created.
	 */
	function set_time_created($time_created)
	{
		$this->set_default_property(self :: PROPERTY_TIME_CREATED, $time_created);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function create()
	{
		$wdm = WebserviceDataManager :: get_instance();
		return $wdm->create_webservice_credential($this);
	}
	
	function update()
	{
		$wdm = WebserviceDataManager :: get_instance();
		return $wdm->update_webservice_credential($this);
	}
	
	function delete()
	{
		$wdm = WebserviceDataManager :: get_instance();
		return $wdm->delete_webservice_credential($this);
	}
}
?>