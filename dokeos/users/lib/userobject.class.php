<?php
/**
 * $Id: learningobject.class.php 11654 2007-03-22 09:06:13Z bmol $
 * @package repository
 */
//require_once dirname(__FILE__).'/accessiblelearningobject.class.php';
//require_once dirname(__FILE__).'/repositorydatamanager.class.php';
//require_once dirname(__FILE__).'/repositoryutilities.class.php';
//require_once dirname(__FILE__).'/condition/equalitycondition.class.php';
//require_once dirname(__FILE__).'/learningobjectdifference.class.php';
//require_once dirname(__FILE__).'/learningobjectdisplay.class.php';
/**
 *	This class represents a user object. 
 *
 *	User objects have a number of default properties:
 *	- user_id: the numeric ID of the learning object;
 *
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */

class UserObject
{
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_LASTNAME = 'lastname';
	const PROPERTY_FIRSTNAME = 'firstname';
	const PROPERTY_USERNAME = 'username';
	const PROPERTY_PASSWORD = 'password';
	const PROPERTY_AUTH_SOURCE = 'auth_source';
	const PROPERTY_EMAIL = 'email';
	const PROPERTY_STATUS = 'status';
	const PROPERTY_PHONE = 'phone';
	const PROPERTY_OFFICIAL_CODE = 'official_code';
	const PROPERTY_PICTURE_URI = 'picture_uri';
	const PROPERTY_CREATOR_ID = 'creator_id';
	const PROPERTY_COMPETENCES = 'competences';
	const PROPERTY_DIPLOMAS = 'diplomas';
	const PROPERTY_OPENAREA = 'openarea';
	const PROPERTY_TEACH = 'teach';
	const PROPERTY_PRODUCTIONS = 'productions';
	const PROPERTY_CHATCALL_USER_ID = 'chatcall_user_id';
	const PROPERTY_CHATCALL_DATE = 'chatcall_date';
	const PROPERTY_CHATCALL_TEXT = 'chatcall_text';
	const PROPERTY_LANGUAGE = 'language';
	const PROPERTY_DISK_QUOTA = 'disk_quota';
	const PROPERTY_DATABASE_QUOTA = 'database_quota';
	const PROPERTY_VERSION_QUOTA = 'version_quota';
	
	/**#@-*/

	/**
	 * Numeric identifier of the user object.
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
	function UserObject($user_id = 0, $defaultProperties = array ())
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
	 * Gets the default properties of this user object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all user objects.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_VISUAL, self :: PROPERTY_DB, self :: PROPERTY_NAME, self :: PROPERTY_PATH, self :: PROPERTY_TITULAR, self :: PROPERTY_LANGUAGE, self :: PROPERTY_EXTLINK_URL, self :: PROPERTY_EXTLINK_NAME, self :: PROPERTY_VISIBILITY, self :: PROPERTY_SUBSCRIBE_ALLOWED, self :: PROPERTY_UNSUBSCRIBE_ALLOWED);
	}
		
	/**
	 * Sets a default property of this user object by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default user object
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
	 * Returns the user_id of this user object.
	 * @return int The user_id.
	 */
	function get_user_id()
	{
		return $this->user_id;
	}
	
	/**
	 * Returns the lastname of this user object
	 * @return String The lastname
	 */
	function get_lastname()
	{
		return $this->get_default_property(self :: PROPERTY_LASTNAME);
	}
	
	/**
	 * Returns the firstname of this userobject
	 * @return String The firstname
	 */
	function get_firstname()
	{
		return $this->get_default_property(self :: PROPERTY_FIRSTNAME);
	}
	
	/**
	 * Returns the username of this userobject
	 * @return String The username
	 */
	function get_username()
	{
		return $this->get_default_property(self :: PROPERTY_USERNAME);
	}
	
	/**
	 * Returns the password of this userobject
	 * @return String The password
	 */
	function get_password()
	{
		return $this->get_default_property(self :: PROPERTY_PASSWORD);
	}
	
	/**
	 * Returns the auth_source for this user object
	 * @return String The auth_source
	 */
	function get_auth_source()
	{
		return $this->get_default_property(self :: PROPERTY_AUTH_SOURCE);
	}
	
	/**
	 * Returns the email for this user object
	 * @return String The email address
	 */
	function get_email()
	{
		return $this->get_default_property(self :: PROPERTY_EMAIL);
	}
	
	/**
	 * Returns the status for this user object
	 * @return Int The status
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}
	
	/**
	 * Returns the official code for this user object
	 * @return String The official code
	 */
	function get_official_code()
	{
		return $this->get_default_property(self :: PROPERTY_PHONE);
	}
	
	/**
	 * Returns the phone number for this user object
	 * @return String The phone number
	 */
	function get_phone()
	{
		return $this->get_default_property(self :: PROPERTY_OFFICIAL_CODE);
	}
	
	/**
	 * Returns the Picture URI for this user object
	 * @return String The URI
	 */
	function get_picture_uri()
	{
		return $this->get_default_property(self :: PROPERTY_PICTURE_URI);
	}
	
	/**
	 * Returns the creator ID for this user object
	 * @return Int The ID
	 */
	function get_creator_id()
	{
		return $this->get_default_property(self :: PROPERTY_CREATOR_ID);
	}
	
	/** 
	 * Returns the competences for this user object
	 * @return String The Competences
	 */
	function get_competences()
	{
		return $this->get_default_property(self :: PROPERTY_COMPETENCES);
	}
	
	/**
	 * Returns the diplomas for this user object
	 * @return String The diplomas
	 */
	function get_diplomas()
	{
		return $this->get_default_property(self :: PROPERTY_DIPLOMAS);
	}
	
	/**
	 * Returns the openarea for this user object
	 * @return String The openarea
	 */
	function get_openarea()
	{
		return $this->get_default_property(self :: PROPERTY_OPENAREA);
	}
	
	
	function get_teach()
	{
		return $this->get_default_property(self :: PROPERTY_TEACH);
	}
	
	function get_productions()
	{
		return $this->get_default_property(self :: PROPERTY_PRODUCTIONS);
	}
	
	/**
	 * Returns the chatcall user id for this user object
	 * @return Int the ID
	 */
	function get_chatcall_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_CHATCALL_USER_ID);
	}
	
	/**
	 * Returns the chatcall date for this user object
	 * @return timestamp the date
	 */
	function get_chatcall_date()
	{
		return $this->get_default_property(self :: PROPERTY_CHATCALL_DATE);
	}
	
	/**
	 * Returns the chatcall text for this user object
	 * @return String the text
	 */
	function get_chatcall_text()
	{
		return $this->get_default_property(self :: PROPERTY_CHATCALL_TEXT);
	}
	
	/**
	 * Returns the language for this user object
	 * @return String the Language
	 */
	function get_language()
	{
		return $this->get_default_property(self :: PROPERTY_LANGUAGE);
	}
	
	/**
	 * Returns the disk quota for this user object
	 * @return Int the disk quota
	 */
	function get_disk_quota()
	{
		return $this->get_default_property(self :: PROPERTY_DISK_QUOTA);
	}
	
	/**
	 * Returns the database quota for this user object
	 * @return Int the database quota
	 */
	function get_database_quota()
	{
		return $this->get_default_property(self :: PROPERTY_DATABASE_QUOTA);
	}
	
	/**
	 * Returns the default version quota for this user object
	 * @return Int the version quota
	 */
	function get_version_quota()
	{
		return $this->get_default_property(self :: PROPERTY_VERSION_QUOTA);
	}
	
	/**
	 * Sets the user_id of this user object.
	 * @param int $user_id The user_id.
	 */
	function set_user_id($user_id)
	{
		$this->user_id = $user_id;
	}		
	
	/**
	 * Sets the lastname of this user object.
	 * @param String $lastname the lastname.
	 */
	function set_lastname($lastname)
	{
		$this->set_default_property(self :: PROPERTY_LASTNAME, $lastname);
	}
	
	/**
	 * Sets the firstname of this user object.
	 * @param String $firstname the firstname.
	 */
	function set_firstname($firstname)
	{
		$this->set_default_property(self :: PROPERTY_FIRSTNAME, $firstname);
	}
	
	/**
	 * Sets the username of this user object.
	 * @param String $username the username.
	 */
	function set_username($username)
	{
		$this->set_default_property(self :: PROPERTY_USERNAME, $username);
	}
	
	/**
	 * Sets the password of this user object.
	 * @param String $password the password.
	 */
	function set_password($password)
	{
		$this->set_default_property(self :: PROPERTY_PASSWORD, $password);
	}
	
	/**
	 * Sets the Auth_source for this user object.
	 * @param String $auth_source the auth source.
	 */
	function set_auth_source($auth_source)
	{
		$this->set_default_property(self :: PROPERTY_AUTH_SOURCE, $auth_source);
	}
	
	/**
	 * Sets the email for this user object.
	 * @param String $email the email.
	 */
	function set_email($email)
	{
		$this->set_default_property(self :: PROPERTY_EMAIL, $email);
	}
	
	/**
	 * Sets the status for this user object.
	 * @param Int $status the status.
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}
	
	/**
	 * Sets the official code for this user object.
	 * @param String $official_code the official code.
	 */
	function set_official_code($official_code)
	{
		$this->set_default_property(self :: PROPERTY_OFFICIAL_CODE, $official_code);
	}
	
	/**
	 * Sets the phone number for this user object.
	 * @param String $phone the phone number
	 */
	function set_phone($phone)
	{
		$this->set_default_property(self :: PROPERTY_PHONE, $phone);
	}
	
	/**
	 * Sets the picture uri for this user object
	 * @param String $picture_uri the picture URI
	 */
	function set_picture_uri($picture_uri)
	{
		$this->set_default_property(self :: PROPERTY_PICTURE_URI, $picture_uri);
	}
	
	/**
	 * Sets the creator ID for this user object.
	 * @param String $creator_id the creator ID.
	 */
	function set_creator_id($creator_id)
	{
		$this->set_default_property(self :: PROPERTY_CREATOR_ID, $creator_id);
	}
	
	/**
	 * Sets the competences for this user object.
	 * @param String $competences the competences.
	 */
	function set_competences($competences)
	{
		$this->set_default_property(self :: PROPERTY_COMPETENCES, $competences);
	}
	
	/**
	 * Sets the diplomas for this user object. 
	 * @param String $diplomas the diplomas.
	 */
	function set_diplomas($diplomas)
	{
		$this->set_default_property(self :: PROPERTY_DIPLOMAS, $diplomas);
	}
	
	/**
	 * Sets the openarea for this user object.
	 * @param String $openarea the openarea.
	 */
	function set_openarea($openarea)
	{
		$this->set_default_property(self :: PROPERTY_OPENAREA, $openarea);
	}
	
	function set_teach($teach)
	{
		$this->set_default_property(self :: PROPERTY_TEACH, $teach);
	}
	
	/**
	 * Sets the productions for this user object.
	 * @param String $productions the productions.
	 */
	function set_productions($productions)
	{
		$this->set_default_property(self :: PROPERTY_PRODUCTIONS, $productions);
	}
	
	/**
	 * Sets the chatcall user id for this user object.
	 * @param Int $user_id the user_id.
	 */
	function set_chatcall_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_CHATCALL_USER_ID, $user_id);
	}
	
	/**
	 * Sets the chatcall date for this user object.
	 * @param timestamp $date the date.
	 */
	function set_chatcall_date($date)
	{
		$this->set_default_property(self :: PROPERTY_CHATCALL_DATE, $date);
	}
	
	/**
	 * Sets the chatcall text for this user object.
	 * @param String $text $text The text.
	 */
	function set_chatcall_text($text)
	{
		$this->set_default_property(self :: PROPERT_CHATCALL_TEXT, $text);
	}
	
	/**
	 * Sets the language for this user object.
	 * @param String $language The language.
	 */
	function set_language($language)
	{
		$this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
	}
	
	/**
	 * Sets the disk quota for this user object.
	 * @param Int $disk_quota The disk quota.
	 */
	function set_disk_quota($disk_quota)
	{
		$this->set_default_property(self :: PROPERTY_DISK_QUOTA, $disk_quota);
	}
	
	/**
	 * Sets the database_quota for this user object.
	 * @param Int $database_quota The database quota.
	 */
	function set_database_quota($database_quota)
	{
		$this->set_default_property(self :: PROPERTY_DATABASE_QUOTA, $database_quota);
	}
	
	/**
	 * Sets the default version quota for this user object.
	 * @param Int $version_quota The version quota.
	 */
	function set_version_quota($version_quota)
	{
		$this->set_default_property(self :: PROPERTY_VERSION_QUOTA, $version_quota);
	}
	
	/**
	 * Instructs the Datamanager to delete this user object.
	 * @return boolean True if success, false otherwise.
	 */
	function delete()
	{
		return UserDataManager :: get_instance()->delete_user_object($this);
	}
}
?>