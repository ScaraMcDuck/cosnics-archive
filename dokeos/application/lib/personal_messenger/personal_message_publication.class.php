<?php
/**
 * @package application.lib.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_user_path(). 'lib/users_data_manager.class.php';

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
class PersonalMessagePublication
{
	const PROPERTY_ID = 'id';
	const PROPERTY_PERSONAL_MESSAGE = 'personal_message';
	const PROPERTY_STATUS = 'status';
	const PROPERTY_USER = 'user';
	const PROPERTY_SENDER = 'sender';
	const PROPERTY_RECIPIENT = 'recipient';
	const PROPERTY_PUBLISHED = 'published';
	
	
	private $id;
	private $defaultProperties;
	
	/**
	 * Creates a new PM object.
	 * @param int $id The numeric ID of the PM object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the PM
	 *                                 object. Associative array.
	 */
	function PersonalMessagePublication($id = 0, $defaultProperties = array ())
	{
		$this->id = $id;
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_PERSONAL_MESSAGE, self :: PROPERTY_STATUS, self :: PROPERTY_USER, self :: PROPERTY_SENDER, self :: PROPERTY_RECIPIENT, self :: PROPERTY_PUBLISHED);
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
		return $this->id;
	}
	
	/**
	 * Returns the learning object id from this PMP object
	 * @return int The personal message ID
	 */
	function get_personal_message()
	{
		return $this->get_default_property(self :: PROPERTY_PERSONAL_MESSAGE);
	}
	 
	/**
	 * Returns the status of this PMP object
	 * @return int the status
	 */
	function get_status()
	{
	 	return $this->get_default_property(self :: PROPERTY_STATUS);
	}
	
	 /**
	  * Returns the user of this PMP object
	  * @return int the user
	  */
	function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER);
	}
	
	 /**
	  * Returns the sender of this PMP object
	  * @return int the sender
	  */
	function get_sender()
	{
		return $this->get_default_property(self :: PROPERTY_SENDER);
	}
	 
	 /**
	  * Returns the recipient of this PMP object
	  * @return int the recipient
	  */
	function get_recipient()
	{
		return $this->get_default_property(self :: PROPERTY_RECIPIENT);
	}
	
	/**
	 * Returns the published timestamp of this PMP object
	 * @return Timestamp the published date
	 */
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	} 
	  
	/**
	 * Sets the id of this PMP.
	 * @param int $pm_id The PM id.
	 */
	function set_id($id)
	{
		$this->id = $id;
	}	
	
	/**
	 * Sets the learning object id of this PMP.
	 * @param Int $id the personal message ID.
	 */
	function set_personal_message($id)
	{
		$this->set_default_property(self :: PROPERTY_PERSONAL_MESSAGE, $id);
	}
	
	/**
	 * Sets the status of this PMP.
	 * @param int $status the Status.
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}
	
	/**
	 * Sets the user of this PMP.
	 * @param int $user the User.
	 */
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}
	
	/**
	 * Sets the sender of this PMP.
	 * @param int $sender the Sender.
	 */
	function set_sender($sender)
	{
		$this->set_default_property(self :: PROPERTY_SENDER, $sender);
	}
	
	/**
	 * Sets the recipient of this PMP.
	 * @param int $recipient the user_id of the recipient.
	 */
	function set_recipient($recipient)
	{
		$this->set_default_property(self :: PROPERTY_RECIPIENT, $recipient);
	}
	
	/**
	 * Sets the published date of this PM.
	 * @param int $published the timestamp of the published date.
	 */
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}
	
	function get_publication_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_object($this->get_personal_message());
	}
	
	function get_publication_sender()
	{
		return $this->get_publication_user($this->get_sender());
	}
	
	function get_publication_recipient()
	{
		return $this->get_publication_user($this->get_recipient());
	}	
	
	function get_publication_user($user_id)
	{
		$udm = UsersDataManager :: get_instance();
		return $udm->retrieve_user($user_id);
	}
	
	/**
	 * Instructs the data manager to create the personal message publication, making it
	 * persistent. Also assigns a unique ID to the publication and sets
	 * the publication's creation date to the current time.
	 * @return boolean True if creation succeeded, false otherwise.
	 */
	function create()
	{
		$now = time();
		$this->set_published($now);
		$pmdm = PersonalMessengerDataManager :: get_instance();
		$id = $pmdm->get_next_personal_message_publication_id();
		$this->set_id($id);
		return $pmdm->create_personal_message_publication($this);
	}
	
	/**
	 * Deletes this publication from persistent storage
	 * @see PersonalMessengerDataManager::delete_personal_message_publication()
	 */
	function delete()
	{
		return PersonalMessengerDataManager :: get_instance()->delete_personal_message_publication($this);
	}
	
	/**
	 * Updates this publication in persistent storage
	 * @see PersonalMessengerDataManager::update_personal_message_publication()
	 */
	function update()
	{
		return PersonalMessengerDataManager :: get_instance()->update_personal_message_publication($this);
	}
}
?>
