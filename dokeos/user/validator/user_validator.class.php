<?php
require_once Path :: get_user_path() . '/lib/data_manager/database.class.php';
require_once Path :: get_user_path() . '/lib/user.class.php';
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
    private $udm;

    function UserValidator()
    {
        $this->udm = DatabaseUserDataManager ::get_instance();
    }

    public function get_required_property_names()
	{
		return array (User :: PROPERTY_USERNAME, User :: PROPERTY_PASSWORD,
                      User :: PROPERTY_AUTH_SOURCE, User :: PROPERTY_STATUS,
					  User :: PROPERTY_DISK_QUOTA, User :: PROPERTY_DATABASE_QUOTA,
					  User :: PROPERTY_VERSION_QUOTA, User :: PROPERTY_ACTIVATION_DATE,
                      User :: PROPERTY_EXPIRATION_DATE, User :: PROPERTY_REGISTRATION_DATE,
                      User :: PROPERTY_ACTIVE);
	}

    function validate_create(&$userProperties)
    {
        if(!$this->validate_properties($userProperties))
        return false;

        if(!$this->udm->is_username_available($userProperties[User :: PROPERTY_USERNAME]))
        return false;
        
        if(!empty($userProperties[User :: PROPERTY_CREATOR_ID]))
        {
            $var = $this->get_person_id($userProperties[User :: PROPERTY_CREATOR_ID]);
            if(!$var)
            return false;
            else
            $userProperties[User :: PROPERTY_CREATOR_ID] = $var;
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
        $var = $this->get_person_id($userProperties[User :: PROPERTY_USERNAME]);
        if(!$var)
        return false;
        else
        $userProperties[User :: PROPERTY_USER_ID] = $var;

        /*
         * To check if the creatorname has an equivalent ID, and if so, swap them.
         */
        if(!empty($userProperties[User :: PROPERTY_CREATOR_ID]))
        {
            $var = $this->get_person_id($userProperties[User :: PROPERTY_CREATOR_ID]);
            if(!$var)
            return false;
            else
            $userProperties[User :: PROPERTY_CREATOR_ID] = $var;
        }

        return true;
    }

    function validate_delete(&$userProperties)
    {
        /*
         * To check if the username exists, and retrieve the ID
         */
        $var = $this->get_person_id($userProperties[User :: PROPERTY_USERNAME]);
        if(!$var)
        return false;
        else
        $userProperties[User :: PROPERTY_USER_ID] = $var;
        
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