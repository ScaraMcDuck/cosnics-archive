<?php
require_once Path :: get_group_path() . '/lib/data_manager/database.class.php';
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
class GroupValidator extends Validator
{
    const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_PARENT = 'parent';

    private $gdm;

    function GroupValidator()
    {
        $this->gdm = DatabaseGroupDataManager ::get_instance();
    }

    public function get_required_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_SORT, self :: PROPERTY_PARENT);
	}

    function validate_create(&$userProperties)
    {
        
    }

    function validate_update(&$userProperties)
    {
        
    }

    function validate_delete(&$userProperties)
    {
        
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

    private function get_group_id($group_name)
    {
        $group = $this->gdm->retrieve_group_by_name($group_name);
        if(isset($group) && count($group->get_default_properties())>0)
        {
           return $group->get_id();
        }
        else
        {
            return false;
        }
    }
}
?>