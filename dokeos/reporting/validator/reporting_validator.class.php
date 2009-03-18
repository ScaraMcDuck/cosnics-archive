<?php
require_once Path :: get_reporting_path() . '/lib/data_manager/database.class.php';
require_once Path :: get_reporting_path() . '/lib/reporting_template_registration.class.php';
require_once Path :: get_library_path() . 'validator/validator.class.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of reporting_validatorclass
 * The purpose of this class is to validate the given Reporting-properties:
 * -To check if all the required properties are there
 * -To check if e.g. the name of a person exists and retrieve the respective ID where necessary
 * @author Samumon
 */
class ReportingValidator extends Validator
{
    private $rdm;

    function ReportingValidator()
    {
        $this->rdm = DatabaseReportingDataManager ::get_instance();
    }

    public function get_required_property_names()
	{
		return array (  ReportingTemplateRegistration :: PROPERTY_ID,
                        ReportingTemplateRegistration :: PROPERTY_TITLE,
                        ReportingTemplateRegistration :: PROPERTY_APPLICATION,
                        ReportingTemplateRegistration :: PROPERTY_CLASSNAME,
                        ReportingTemplateRegistration :: PROPERTY_PLATFORM,
                        ReportingTemplateRegistration :: PROPERTY_DESCRIPTION);
	}

    function validate_retrieve(&$reportingProperties)
    {
        if($reportingProperties[ID]==null)
        return false;

        return true;
    }

    function validate_create(&$reportingProperties)
    {
        if(!$this->validate_properties($reportingProperties,$this->get_required_property_names()))
        return false;

        if(!$this->validate_property_names($groupProperties, User :: get_default_property_names()))
        return false;

        if(!$this->udm->is_username_available($userProperties[User :: PROPERTY_USERNAME]))
        return false;

        if(!$this->check_quota($userProperties))
        return false;

        if(!$this->check_dates($userProperties))
        return false;
        
        if(!empty($userProperties[User :: PROPERTY_CREATOR_ID]))
        {
            $var = $this->get_person_id($userProperties[User :: PROPERTY_CREATOR_ID]);
            if(!$var)
            return false;
            else
            $userProperties[User :: PROPERTY_CREATOR_ID] = $var;
        }

        if($userProperties[User :: PROPERTY_ACTIVE] !=='0' && $userProperties[User :: PROPERTY_ACTIVE] !=='1' && $userProperties[User :: PROPERTY_ACTIVE] !== false && $userProperties[User :: PROPERTY_ACTIVE] !== true)
        return false;

        return true;
    }

    function validate_update(&$userProperties)
    {
        if(!$this->validate_properties($userProperties,$this->get_required_property_names()))
        return false;

        if(!$this->validate_property_names($userProperties, User :: get_default_property_names()))
        return false;

        /*
         * To check if the user_id exists.
         */
        if(!$this->does_user_exist($userProperties[user_id]))
        return false;

        /*
         * To check if the creator exists and retrieve it's ID.
         */
        if(!empty($userProperties[User :: PROPERTY_CREATOR_ID]))
        {
            $var = $this->get_person_id($userProperties[User :: PROPERTY_CREATOR_ID]);
            if(!$var)
            return false;
            else
            $userProperties[User :: PROPERTY_CREATOR_ID] = $var;
        }

        if(!$this->check_quota($userProperties))
        return false;

        if(!$this->check_dates($userProperties))
        return false;

        if($userProperties[User :: PROPERTY_ACTIVE] !=='0' && $userProperties[User :: PROPERTY_ACTIVE] !=='1' && $userProperties[User :: PROPERTY_ACTIVE] !== false && $userProperties[User :: PROPERTY_ACTIVE] !== true)
        return false;

        return true;
    }

    function validate_delete(&$userProperties)
    {
        if(!$this->validate_property_names($groupProperties, User :: get_default_property_names()))
        return false;

        if($userProperties[username]==null)
        return false;
        
        /*
         * To check if the user_id exists.
         */
        if(!$this->does_user_exist($userProperties[user_id]))
        return false;
        
        return true;
    }

    /*private function get_reporting_id($report_title)
    {
        $report = $this->rdm->retrieve_user_by_username($person_name);
        if(isset($user) && count($user->get_default_properties())>0)
        {
           return $user->get_id();
        }
        else
        {
            return false;
        }
    }*/

    private function does_report_exist($reporting_id)
    {
        return $this->rdm->count_reporting_template_registrations(new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_ID, $reporting_id))!=0;
    }

    

    
}
?>