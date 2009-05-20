<?php

/**
 * Base class for a reporting manager component.
 * A reporting manager provides different tools to the administrator.
 */

require_once Path :: get_library_path() . 'core_application_component.class.php';

 /**
 * @author Michael Kyndt
 */

abstract class ReportingManagerComponent extends CoreApplicationComponent  
{
	/**
	 * Constructor
	 * @param ReportingManager $reporting_manager The reporting manager which
	 * provides this component
	 */
    function ReportingManagerComponent($reporting_manager) 
    {
    	parent :: __construct($reporting_manager);
    }
    
    function count_reporting_template_registrations($condition = null)
	{
        return $this->get_parent()->count_reporting_template_registrations($condition);
	}

    function retrieve_reporting_template_registrations($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
        return $this->get_parent()->retrieve_reporting_template_registrations($condition, $offset, $count, $order_property, $order_direction);
	}

    function retrieve_reporting_template_registration($reporting_template_registration_id)
    {
        return $this->get_parent()->retrieve_reporting_template_registration($reporting_template_registration_id);
    }

    function get_reporting_template_registration_viewing_url($reporting_template_registration)
	{
		return $this->get_parent()->get_reporting_template_registration_viewing_url($reporting_template_registration);
	}

    function get_reporting_template_registration_editing_url($reporting_template_registration)
    {
        return $this->get_parent()->get_reporting_template_registration_editing_url($reporting_template_registration);
    }
}
?>