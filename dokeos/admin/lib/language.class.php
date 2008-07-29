<?php
/**
 * @package admin.lib
 * @author Hans De Bisschop
 */

require_once dirname(__FILE__).'/admin_data_manager.class.php';

class Language
{
	const PROPERTY_ID				= 'id';
	const PROPERTY_ORIGINAL_NAME	= 'original_name';
	const PROPERTY_ENGLISH_NAME		= 'english_name';
	const PROPERTY_ISOCODE			= 'isocode';
	const PROPERTY_FOLDER			= 'folder';
	const PROPERTY_AVAILABLE		= 'available';
	
	
	private $id;
	private $defaultProperties;
	
	/**
	 * Creates a new PM object.
	 * @param int $id The numeric ID of the PM object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the PM
	 *                                 object. Associative array.
	 */
	function Language($id = 0, $defaultProperties = array ())
	{
		$this->set_id($id);
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this PM object by name.
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
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Get the default properties of all users.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_ORIGINAL_NAME, self :: PROPERTY_ENGLISH_NAME, self :: PROPERTY_ISOCODE, self :: PROPERTY_FOLDER, self :: PROPERTY_AVAILABLE);
	}
	
	/**
	 * Sets a default property of this PMP by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default PMP
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
	 * Returns the id of this PMP.
	 * @return int The PM id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the learning object id from this PMP object
	 * @return int The personal message ID
	 */
	function get_original_name()
	{
		return $this->get_default_property(self :: PROPERTY_ORIGINAL_NAME);
	}
	 
	/**
	 * Returns the status of this PMP object
	 * @return int the status
	 */
	function get_english_name()
	{
	 	return $this->get_default_property(self :: PROPERTY_ENGLISH_NAME);
	}
	
	 /**
	  * Returns the user of this PMP object
	  * @return int the user
	  */
	function get_isocode()
	{
		return $this->get_default_property(self :: PROPERTY_ISOCODE);
	}
	
	 /**
	  * Returns the sender of this PMP object
	  * @return int the sender
	  */
	function get_folder()
	{
		return $this->get_default_property(self :: PROPERTY_FOLDER);
	}
	 
	 /**
	  * Returns the recipient of this PMP object
	  * @return int the recipient
	  */
	function get_available()
	{
		return $this->get_default_property(self :: PROPERTY_AVAILABLE);
	}
	  
	/**
	 * Sets the id of this PMP.
	 * @param int $pm_id The PM id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}	
	
	/**
	 * Sets the learning object id of this PMP.
	 * @param Int $id the personal message ID.
	 */
	function set_original_name($original_name)
	{
		$this->set_default_property(self :: PROPERTY_ORIGINAL_NAME, $original_name);
	}
	
	/**
	 * Sets the status of this PMP.
	 * @param int $status the Status.
	 */
	function set_english_name($english_name)
	{
		$this->set_default_property(self :: PROPERTY_ENGLISH_NAME, $english_name);
	}
	
	/**
	 * Sets the user of this PMP.
	 * @param int $user the User.
	 */
	function set_isocode($isocode)
	{
		$this->set_default_property(self :: PROPERTY_ISOCODE, $isocode);
	}
	
	/**
	 * Sets the sender of this PMP.
	 * @param int $sender the Sender.
	 */
	function set_folder($folder)
	{
		$this->set_default_property(self :: PROPERTY_FOLDER, $folder);
	}
	
	/**
	 * Sets the recipient of this PMP.
	 * @param int $recipient the user_id of the recipient.
	 */
	function set_available($available)
	{
		$this->set_default_property(self :: PROPERTY_AVAILABLE, $available);
	}
	
	function is_available ()
	{
		return $this->get_available();
	}
	
	/**
	 * Instructs the data manager to create the personal message publication, making it
	 * persistent. Also assigns a unique ID to the publication and sets
	 * the publication's creation date to the current time.
	 * @return boolean True if creation succeeded, false otherwise.
	 */
	function create()
	{
		$adm = AdminDataManager :: get_instance();
		$id = $adm->get_next_language_id();
		$this->set_id($id);
		return $adm->create_language($this);
	}
	
	/**
	 * Deletes this publication from persistent storage
	 * @see PersonalMessengerDataManager::delete_personal_message_publication()
	 */
	function delete()
	{
		return AdminDataManager :: get_instance()->delete_language($this);
	}
	
	/**
	 * Updates this publication in persistent storage
	 * @see PersonalMessengerDataManager::update_personal_message_publication()
	 */
	function update()
	{
		return AdminDataManager :: get_instance()->update_language($this);
	}
}
?>
