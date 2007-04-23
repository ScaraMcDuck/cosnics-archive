<?php
/**
 *	This class represents a personal message. 
 *
 *	personal message (PM) objects have a number of default properties:
 *	- id: the numeric ID of the PM;
 *	- learning_object: the numeric object ID of the PM (from the repository);
 *	- status: the status of the PM: read/unread/...;
 *	- recipient: the recipient of the PM;
 *	- publisher: the publisher of the PM;
 *	- published: the date when the PM was "posted";
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */
class PersonalMessage
{
	const PROPERTY_PERSONAL_MESSAGE_ID = '';
	const PROPERTY_LEARNING_OBJECT_ID = '';
	const PROPERTY_STATUS = '';
	const PROPERTY_RECIPIENT = '';
	const PROPERTY_PUBLISHER = '';
	const PROPERTY_PUBLISHED = '';
	
	
	private $pm_id;
	private $defaultProperties;
	
	/**
	 * Creates a new PM object.
	 * @param int $id The numeric ID of the PM object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the PM
	 *                                 object. Associative array.
	 */
	function personal_message($pm_id = 0, $defaultProperties = array ())
	{
		$this->pm_id = $pm_id;
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
	
	/**
	 * Get the default properties of all users.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_PERSONAL_MESSAGE_ID, self :: PROPERTY_LEARNING_OBJECT_ID, self :: PROPERTY_STATUS, self :: PROPERTY_RECIPIENT, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED);
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
	 * Returns the id of this PM.
	 * @return int The PM id.
	 */
	function get_personal_message_id()
	{
		return $this->pm_id;
	}
	
	/**
	 * Returns the learning object id from this PM object
	 * @return int The learning object ID
	 */
	function get_learning_object_id()
	{
		return $this->get_default_property(self :: PROPERTY_PERSONAL_MESSAGE_ID);
	}
	 
	/**
	 * Returns the status of this PM object
	 * @return int the status
	 */
	function get_status()
	{
	 	return $this->get_default_property(self :: PROPERTY_STATUS);
	}
	 
	 /**
	  * Returns the recipient of this PM object
	  * @return string the recipient
	  */
	function get_recipient()
	{
		return $this->get_default_property(self :: PROPERTY_RECIPIENT);
	}
	
	/**
	 * Returns the publisher of this PM object
	 * @return String the publisher
	 */
	function get_publisher()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER);
	}
	
	/**
	 * Returns the published timestamp of this PM object
	 * @return Timestamp the published date
	 */
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	} 
	  
	/**
	 * Sets the id of this PM.
	 * @param int $pm_id The PM id.
	 */
	function set_personal_message_id($pm_id)
	{
		$this->pm_id = $pm_id;
	}	
	
	/**
	 * Sets the learning object id of this PM.
	 * @param Int $id the learning object ID.
	 */
	function set_learning_object_message_id($id)
	{
		$this->set_default_property(self :: PROPERTY_PERSONAL_MESSAGE_ID, $id);
	}
	
	/**
	 * Sets the status of this PM.
	 * @param int $status the Status.
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}
	
	/**
	 * Sets the recipient of this PM.
	 * @param int $recipient the user_id of the recipient.
	 */
	function set_recipient($recipient)
	{
		$this->set_default_property(self :: PROPERTY_RECIPIENT, $recipient);
	}

	/**
	 * Sets the publisher of this PM.
	 * @param int $publisher the user_id of the publisher.
	 */
	function set_publisher($publisher)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
	}
	
	/**
	 * Sets the published date of this PM.
	 * @param int $published the timestamp of the published date.
	 */
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}
}
?>
