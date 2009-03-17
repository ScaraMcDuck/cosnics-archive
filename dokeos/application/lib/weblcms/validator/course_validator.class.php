<?php
require_once Path :: get_application_path() . '/lib/weblcms/data_manager/database.class.php';
require_once Path :: get_user_path() . '/libdata_manager/database.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/data_manager/database.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/course/course.class.php';
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
class CourseValidator extends Validator
{
    private $udm;
    private $wdm;

    function CourseValidator()
    {
        $this->udm = DatabaseUserDataManager ::get_instance();
        $this->wdm = DatabaseWeblcmsDataManager ::get_instance();
    }

    private function get_required_course_property_names()
	{
        return array(Course :: PROPERTY_ID, Course ::PROPERTY_CATEGORY);
  	}

    function validate_retrieve(&$courseProperties)
    {
        if($courseProperties[name]==null)
        return false;

        return true;
    }

    function validate_create(&$courseProperties)
    {
        if(!$this->validate_properties($courseProperties,$this->get_required_course_property_names()))
        return false;

        if(!$this->validate_property_names($courseProperties, Course :: get_default_property_names()))
        return false;

        
    }

    function validate_update(&$courseProperties)
    {
        
    }

    function validate_delete(&$courseProperties)
    {
        
    }

    function validate_subscribe_or_unsubscribe(&$input_group_rel_user)
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

    private function does_course_exist(&$courseProperties)
    {
        
    }
}
?>