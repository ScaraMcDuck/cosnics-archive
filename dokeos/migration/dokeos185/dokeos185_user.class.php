<?php

/**
 * @package migration.dokeos185
 */

require_once dirname(__FILE__).'/../lib/import_user.class.php';

/**
 * This class represents an old Dokeos 1.8.5 user
 *
 * @author David Van Wayenbergh
 */

class Dokeos185_User extends Import
{
	
	// Properties Table User
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
	const PROPERTY_LANGUAGE = 'language';
	const PROPERTY_COMPETENCES = 'competences';
	const PROPERTY_DIPLOMAS = 'diplomas';
	const PROPERTY_OPENAREA = 'openarea';
	const PROPERTY_TEACH = 'teach';
	const PROPERTY_PRODUCTIONS = 'productions';
	const PROPERTY_CHATCALL_USER_ID = 'chatcall_user_id';
	const PROPERTY_CHATCALL_DATE = 'chatcall_date';
	const PROPERTY_CHATCALL_TEXT = 'chatcall_text';
	const PROPERTY_REGISTRATION_DATE = 'registration_date';
	const PROPERTY_EXPIRATION_DATE = 'expiration_date';
	const PROPERTY_ACTIVE = 'active';
	const PROPERTY_OPENID = 'openid';
	
	// Property Table Admin
	const PROPERTY_ADMIN_USER_ID = 'user_id';
	
	const ACTION_READ_USER = 'read';
	
	
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
	function User($defaultProperties = array ())
	{
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
		return array (self :: PROPERTY_USER_ID, self :: PROPERTY_LASTNAME, self :: PROPERTY_FIRSTNAME, self :: PROPERTY_USERNAME, self :: PROPERTY_PASSWORD, self :: PROPERTY_AUTH_SOURCE, self :: PROPERTY_EMAIL, self :: PROPERTY_STATUS, self :: PROPERTY_PLATFORMADMIN, self :: PROPERTY_PHONE, self :: PROPERTY_OFFICIAL_CODE, self ::PROPERTY_PICTURE_URI, self :: PROPERTY_CREATOR_ID, self :: PROPERTY_LANGUAGE, self :: PROPERTY_DISK_QUOTA, self :: PROPERTY_DATABASE_QUOTA, self :: PROPERTY_VERSION_QUOTA);
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
	 * Returns the lastname of this user.
	 * @return String The lastname
	 */
	function get_lastname()
	{
		return $this->get_default_property(self :: PROPERTY_LASTNAME);
	}
	
	/**
	 * Returns the firstname of this user.
	 * @return String The firstname
	 */
	function get_firstname()
	{
		return $this->get_default_property(self :: PROPERTY_FIRSTNAME);
	}
	
	/**
	 * Returns the fullname of this user
	 * @return string The fullname
	 */
	 function get_fullname()
	 {
	 	//@todo Make format of fullname configurable somewhere
	 	return $this->get_firstname().' '.$this->get_lastname();
	 }
	 
	 /**
	 * Returns the username of this user.
	 * @return String The username
	 */
	function get_username()
	{
		return $this->get_default_property(self :: PROPERTY_USERNAME);
	}
	
	/**
	 * Returns the password of this user.
	 * @return String The password
	 */
	function get_password()
	{
		return $this->get_default_property(self :: PROPERTY_PASSWORD);
	}
	
	/**
	 * Returns the auth_source for this user.
	 * @return String The auth_source
	 */
	function get_auth_source()
	{
		return $this->get_default_property(self :: PROPERTY_AUTH_SOURCE);
	}
	
	/**
	 * Returns the email for this user.
	 * @return String The email address
	 */
	function get_email()
	{
		return $this->get_default_property(self :: PROPERTY_EMAIL);
	}
	
	/**
	 * Returns the status for this user.
	 * @return Int The status
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}
	
	/**
	 * Returns the official code for this user.
	 * @return String The official code
	 */
	function get_official_code()
	{
		return $this->get_default_property(self :: PROPERTY_OFFICIAL_CODE);
	}
	
	/**
	 * Returns the phone number for this user.
	 * @return String The phone number
	 */
	function get_phone()
	{
		return $this->get_default_property(self :: PROPERTY_PHONE);
	}
	
	/**
	 * Returns the Picture URI for this user.
	 * @return String The URI
	 */
	function get_picture_uri()
	{
		return $this->get_default_property(self :: PROPERTY_PICTURE_URI);
	}
	
	/**
	 * Returns the creator ID for this user.
	 * @return Int The ID
	 */
	function get_creator_id()
	{
		return $this->get_default_property(self :: PROPERTY_CREATOR_ID);
	}
	
	/**
	 * Returns the language for this user.
	 * @return String the Language
	 */
	function get_language()
	{
		return $this->get_default_property(self :: PROPERTY_LANGUAGE);
	}
	
	/**
	 * Returns the competences for this user.
	 * @return String the Competences
	 */
	 function get_competences()
	{
		return $this->get_default_property(self :: PROPERTY_COMPETENCES);
	}
	
	/**
	 * Returns the diplomas for this user.
	 * @return String the Diplomas
	 */
	 function get_diplomas()
	{
		return $this->get_default_property(self :: PROPERTY_DIPLOMAS);
	}
	/**
	 * Returns the openarea for this user.
	 * @return String the Competences
	 */
	 function get_openarea()
	{
		return $this->get_default_property(self :: PROPERTY_OPENAREA);
	}
	
	/**
	 * Returns teach for this user.
	 * @return String Teach
	 */
	 function get_teach()
	{
		return $this->get_default_property(self :: PROPERTY_TEACH);
	}
	
	/**
	 * Returns the productions for this user.
	 * @return String the Productions
	 */
	 function get_productions()
	{
		return $this->get_default_property(self :: PROPERTY_PRODUCTIONS);
	}
	
	/**
	 * Returns the chatcall_user_id for this user.
	 * @return int the Chatcall_user_id
	 */
	 function get_chatcall_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_CHATCALL_USER_ID);
	}
	
	/**
	 * Returns the chatcall_date for this user.
	 * @return String the chatcall_date
	 */
	 function get_chatcall_date()
	{
		return $this->get_default_property(self :: PROPERTY_CHATCALL_DATE);
	}
	
	/**
	 * Returns the chatcall_text for this user.
	 * @return String the Chatcall_text
	 */
	 function get_chatcall_text()
	{
		return $this->get_default_property(self :: PROPERTY_CHATCALL_TEXT);
	}
	
	/**
	 * Returns the registration_date for this user.
	 * @return String the Registration_date
	 */
	 function get_registration_date()
	{
		return $this->get_default_property(self :: PROPERTY_REGISTRATION_DATE);
	}
	
	/**
	 * Returns the expiration_date for this user.
	 * @return String the Expiration_date
	 */
	 function get_expiration_date()
	{
		return $this->get_default_property(self :: PROPERTY_EXPIRATION_DATE);
	}
	
	/**
	 * Returns active for this user.
	 * @return int active
	 */
	 function get_active()
	{
		return $this->get_default_property(self :: PROPERTY_ACTIVE);
	}
	
	/**
	 * Returns the openid for this user.
	 * @return String the Openid
	 */
	 function get_openid()
	{
		return $this->get_default_property(self :: PROPERTY_OPENID);
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
	 * Sets the lastname of this user.
	 * @param String $lastname the lastname.
	 */
	function set_lastname($lastname)
	{
		$this->set_default_property(self :: PROPERTY_LASTNAME, $lastname);
	}
	
	/**
	 * Sets the firstname of this user.
	 * @param String $firstname the firstname.
	 */
	function set_firstname($firstname)
	{
		$this->set_default_property(self :: PROPERTY_FIRSTNAME, $firstname);
	}
	
	/**
	 * Sets the username of this user.
	 * @param String $username the username.
	 */
	function set_username($username)
	{
		$this->set_default_property(self :: PROPERTY_USERNAME, $username);
	}
	
	/**
	 * Sets the password of this user. If Dokeos configuration is set to encrypt
	 * the password, this function will also take care of that.
	 * @param String $password the password.
	 */
	function set_password($password)
	{
		$this->set_default_property(self :: PROPERTY_PASSWORD, $password);
	}
	
	/**
	 * Sets the Auth_source for this user.
	 * @param String $auth_source the auth source.
	 */
	function set_auth_source($auth_source)
	{
		$this->set_default_property(self :: PROPERTY_AUTH_SOURCE, $auth_source);
	}
	
	/**
	 * Sets the email for this user.
	 * @param String $email the email.
	 */
	function set_email($email)
	{
		$this->set_default_property(self :: PROPERTY_EMAIL, $email);
	}
	
	/**
	 * Sets the status for this user.
	 * @param Int $status the status.
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}
	
	/**
	 * Sets the official code for this user.
	 * @param String $official_code the official code.
	 */
	function set_official_code($official_code)
	{
		$this->set_default_property(self :: PROPERTY_OFFICIAL_CODE, $official_code);
	}
	
	/**
	 * Sets the phone number for this user.
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
	 * Sets the creator ID for this user.
	 * @param String $creator_id the creator ID.
	 */
	function set_creator_id($creator_id)
	{
		$this->set_default_property(self :: PROPERTY_CREATOR_ID, $creator_id);
	}
	
	/**
	 * Sets the language for this user.
	 * @param String $language The language.
	 */
	function set_language($language)
	{
		$this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
	}
	
	/**
	 * Sets the competences for this user.
	 * @param String $competences The competences.
	 */
	function set_competences($competences)
	{
		$this->set_default_property(self :: PROPERTY_COMPETENCES, $competences);
	}
	
	/**
	 * Sets the diplomas for this user.
	 * @param String $diplomas The diplomas.
	 */
	function set_language($diplomas)
	{
		$this->set_default_property(self :: PROPERTY_DIPLOMAS, $diplomas);
	}
}
?>
