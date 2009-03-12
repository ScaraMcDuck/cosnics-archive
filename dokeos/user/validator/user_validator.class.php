<?php
require_once Path :: get_user_path() . '/lib/data_manager/database.class.php';
require_once Path :: get_library_path() . 'validator/validator.class.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user_validatorclass
 * The purpose of this class is to validate the given User-properties:
 * -To check if all the required properties are there
 * -To check if e.g. the name of a person exists and retrieve the respective ID where necessary
 * @author Samumon
 */
class UserValidator extends Validator
{
    const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_LASTNAME = 'lastname';
	const PROPERTY_FIRSTNAME = 'firstname';
	const PROPERTY_USERNAME = 'username';
	const PROPERTY_PASSWORD = 'password';
	const PROPERTY_AUTH_SOURCE = 'auth_source';
	const PROPERTY_EMAIL = 'email';
	const PROPERTY_STATUS = 'status';
	const PROPERTY_PLATFORMADMIN = 'admin';
	const PROPERTY_PHONE = 'phone';
	const PROPERTY_OFFICIAL_CODE = 'official_code';
	const PROPERTY_PICTURE_URI = 'picture_uri';
	const PROPERTY_CREATOR_ID = 'creator_id';
	const PROPERTY_LANGUAGE = 'language';
	const PROPERTY_DISK_QUOTA = 'disk_quota';
	const PROPERTY_DATABASE_QUOTA = 'database_quota';
	const PROPERTY_VERSION_QUOTA = 'version_quota';
	const PROPERTY_THEME = 'theme';
	const PROPERTY_ACTIVATION_DATE = 'activation_date';
	const PROPERTY_EXPIRATION_DATE = 'expiration_date';
	const PROPERTY_REGISTRATION_DATE = 'registration_date';
	const PROPERTY_ACTIVE = 'active';

    private $udm;

    function UserValidator()
    {
        $this->udm = DatabaseUserDataManager ::get_instance();
    }

    public function get_required_property_names()
	{
		return array (self :: PROPERTY_USERNAME, self :: PROPERTY_PASSWORD,
                      self :: PROPERTY_AUTH_SOURCE, self :: PROPERTY_STATUS,
					  self :: PROPERTY_DISK_QUOTA, self :: PROPERTY_DATABASE_QUOTA,
					  self :: PROPERTY_VERSION_QUOTA, self :: PROPERTY_ACTIVATION_DATE,
                      self :: PROPERTY_EXPIRATION_DATE, self :: PROPERTY_REGISTRATION_DATE,
                      self :: PROPERTY_ACTIVE);
	}

    function validate_create(&$userProperties)
    {
        if(!$this->validate_properties($userProperties))
        return false;

        if(!$this->udm->is_username_available($userProperties[self :: PROPERTY_USERNAME]))
        return false;
        
        if(!empty($userProperties[self :: PROPERTY_CREATOR_ID]))
        {
            $var = $this->get_person_id($userProperties[self :: PROPERTY_CREATOR_ID]);
            if(!$var)
            return false;
            else
            $userProperties[self :: PROPERTY_CREATOR_ID] = $var;
        }

        return true;
    }

    function validate_update(&$userProperties)
    {
        if(!$this->validate_properties($userProperties))
        return false;

        /*
         * To check if the username exists, and retrieve the ID
         */
        $var = $this->get_person_id($userProperties[self :: PROPERTY_USERNAME]);
        if(!$var)
        return false;
        else
        $userProperties[self :: PROPERTY_USER_ID] = $var;

        /*
         * To check if the creatorname has an equivalent ID, and if so, swap them.
         */
        if(!empty($userProperties[self :: PROPERTY_CREATOR_ID]))
        {
            $var = $this->get_person_id($userProperties[self :: PROPERTY_CREATOR_ID]);
            if(!$var)
            return false;
            else
            $userProperties[self :: PROPERTY_CREATOR_ID] = $var;
        }

        return true;
    }

    function validate_delete(&$userProperties)
    {
        /*
         * To check if the username exists, and retrieve the ID
         */
        $var = $this->get_person_id($userProperties[self :: PROPERTY_USERNAME]);
        if(!$var)
        return false;
        else
        $userProperties[self :: PROPERTY_USER_ID] = $var;
        
        return true;
    }

    private function get_person_id($person_name)
    {
        $user = $this->udm->retrieve_user_by_username($person_name);
        if(isset($user) && count($user->get_default_properties())>0)
        {
           return $user->get_id();
        }
        else
        {
            return false;
        }
    }
}
?>