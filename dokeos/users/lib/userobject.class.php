<?php
/**
 * $Id: learningobject.class.php 11654 2007-03-22 09:06:13Z bmol $
 * @package repository
 */
require_once dirname(__FILE__).'/accessiblelearningobject.class.php';
require_once dirname(__FILE__).'/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/repositoryutilities.class.php';
require_once dirname(__FILE__).'/condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/learningobjectdifference.class.php';
require_once dirname(__FILE__).'/learningobjectdisplay.class.php';
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
	const PROPERTY_USERID = 'user';
	const PROPERTY_LASTNAME = 'lastname';
	const PROPERT_FIRSTNAME = 'firstname';
	const PROPERTY_USERNAME = 'username';
	const PROPERTY_PASSWORD = 'password';
	const PROPERTY_AUTH_SOURCE = 'auth_source';
	const PROPERTY_EMAIL = 'email';
	const PROPERTY_STATUS = 'status';
	const PROPERTY_PHONE = 'phone';
	const PROPERTY_OFFICIAL_CODE = 'official_code';
	const PROPERTY_PICTURE_URI = 'picture';
	const PROPERTY_CREATOR_ID = 'creator';
	const PROPERTY_COMPETENCES = 'competences';
	const PROPERTY_DIPLOMAS = 'diplomas';
	const PROPERTY_OPENAREA = 'area';
	const PROPERTY_TEACH = 'teach';
	const PROPERTY_PRODUCTIONS = 'productions';
	const PROPERTY_CHATCALL_USER_ID = 'chatcall_user';
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
	
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_VISUAL, self :: PROPERTY_DB, self :: PROPERTY_NAME, self :: PROPERTY_PATH, self :: PROPERTY_TITULAR, self :: PROPERTY_LANGUAGE, self :: PROPERTY_EXTLINK_URL, self :: PROPERTY_EXTLINK_NAME, self :: PROPERTY_VISIBILITY, self :: PROPERTY_SUBSCRIBE_ALLOWED, self :: PROPERTY_UNSUBSCRIBE_ALLOWED);
	}
		
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	function get_user_id()
	{
		return $this->user_id;
	}
	
	function get_lastname()
	{
		return $this->get_default_property(self :: PROPERTY_LASTNAME);
	}
	
	function get_firstname()
	{
		return $this->get_default_property(self :: PROPERTY_FIRSTNAME);
	}
	
	function get_username()
	{
		return $this->get_default_property(self :: PROPERTY_USERNAME);
	}
	
	function get_password()
	{
		return $this->get_default_property(self :: PROPERTY_PASSWORD);
	}
	
	function get_auth_source()
	{
		return $this->get_default_property(self :: PROPERTY_AUTH_SOURCE);
	}
	
	function get_email()
	{
		return $this->get_default_property(self :: PROPERTY_EMAIL);
	}
	
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}
	
	function get_official_code()
	{
		return $this->get_default_property(self :: PROPERTY_PHONE);
	}
	
	function get_phone()
	{
		return $this->get_default_property(self :: PROPERTY_OFFICIAL_CODE);
	}
	
	function get_picture_uri()
	{
		return $this->get_default_property(self :: PROPERTY_PICTURE_URI);
	}
	
	function get_creator_id()
	{
		return $this->get_default_property(self :: PROPERTY_CREATOR_ID);
	}
	
	function get_competences()
	{
		return $this->get_default_property(self :: PROPERTY_COMPETENCES);
	}
	
	function get_diplomas()
	{
		return $this->get_default_property(self :: PROPERTY_DIPLOMAS);
	}
	
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
	
	function get_chatcall_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_CHATCALL_USER_ID);
	}
	
	function get_chatcall_date()
	{
		return $this->get_default_property(self :: PROPERTY_CHATCALL_DATE);
	}
	
	function get_chatcall_text()
	{
		return $this->get_default_property(self :: PROPERTY_CHATCALL_TEXT);
	}
	
	function get_language()
	{
		return $this->get_default_property(self :: PROPERTY_LANGUAGE);
	}
	
	function get_disk_quota()
	{
		return $this->get_default_property(self :: PROPERTY_DISK_QUOTA);
	}
	
	function get_database_quota()
	{
		return $this->get_default_property(self :: PROPERTY_DATABASE_QUOTA);
	}
	
	function get_version_quota()
	{
		return $this->get_default_property(self :: PROPERTY_VERSION_QUOTA);
	}
	
	function set_user_id($user_id)
	{
		$this->user_id = $user_id;
	}		
	
	function set_lastname($lastname)
	{
		$this->set_default_property(self :: PROPERTY_LASTNAME, $lastname);
	}
	
	function set_firstname($firstname)
	{
		$this->set_default_property(self :: PROPERTY_FIRSTNAME, $firstname);
	}
	
	function set_username($username)
	{
		$this->set_default_property(self :: PROPERTY_USERNAME, $username);
	}
	
	function set_password($password)
	{
		$this->set_default_property(self :: PROPERTY_PASSWORD, $password);
	}
	
	function set_auth_source($auth_source)
	{
		$this->set_default_property(self :: PROPERTY_AUTH_SOURCE, $auth_source);
	}
	
	function set_email($email)
	{
		$this->set_default_property(self :: PROPERTY_EMAIL, $email);
	}
	
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}
	
	function set_official_code($official_code)
	{
		$this->set_default_property(self :: PROPERTY_OFFICIAL_CODE, $official_code);
	}
	
	function set_phone($phone)
	{
		$this->set_default_property(self :: PROPERTY_PHONE, $phone);
	}
	
	function set_picture_uri($picture_uri)
	{
		$this->set_default_property(self :: PROPERTY_PICTURE_URI, $picture_uri);
	}
	
	function set_creator_id($creator_id)
	{
		$this->set_default_property(self :: PROPERTY_CREATOR_ID, $creator_id);
	}
	
	function set_competences($competences)
	{
		$this->set_default_property(self :: PROPERTY_COMPETENCES, $competences);
	}
	
	function set_diplomas($diplomas)
	{
		$this->set_default_property(self :: PROPERTY_DIPLOMAS, $diplomas);
	}
	
	function set_openarea($openarea)
	{
		$this->set_default_property(self :: PROPERTY_OPENAREA, $openarea);
	}
	
	function set_teach($teach)
	{
		$this->set_default_property(self :: PROPERTY_TEACH, $teach);
	}
	
	function set_productions($productions)
	{
		$this->set_default_property(self :: PROPERTY_PRODUCTIONS, $productions);
	}
	
	function set_chatcall_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_CHATCALL_USER_ID, $user_id);
	}
	
	function set_chatcall_date($date)
	{
		$this->set_default_property(self :: PROPERTY_CHATCALL_DATE, $date);
	}
	
	function set_chatcall_text($text)
	{
		$this->set_default_property(self :: PROPERT_CHATCALL_TEXT, $text);
	}
	
	function set_language($language)
	{
		$this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
	}
	
	function set_disk_quota($disk_quota)
	{
		$this->set_default_property(self :: PROPERTY_DISK_QUOTA, $disk_quota);
	}
	
	function set_database_quota($database_quota)
	{
		$this->set_default_property(self :: PROPERTY_DATABASE_QUOTA, $database_quota);
	}
	
	function set_version_quota($version_quota)
	{
		$this->set_default_property(self :: PROPERTY_VERSION_QUOTA, $version_quota);
	}
	
	function delete()
	{
		return UserDataManager :: get_instance()->delete_user_object($this);
	}
}
?>