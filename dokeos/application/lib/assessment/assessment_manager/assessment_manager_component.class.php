<?php

require_once Path :: get_application_path() . 'lib/web_application_component.class.php';

/**
 * @package application.lib.assessment.assessment_manager
 * Basic functionality of a component to talk with the assessment application
 *
 * @author Sven Vanpoucke
 * @author 
 */
abstract class AssessmentManagerComponent extends WebApplicationComponent
{
	/**
	 * Constructor
	 * @param Assessment $assessment The assessment which
	 * provides this component
	 */
	function AssessmentManagerComponent($assessment)
	{
		parent :: __construct($assessment);
	}

	//Data Retrieval

	function count_assessment_publications($condition)
	{
		return $this->get_parent()->count_assessment_publications($condition);
	}

	function retrieve_assessment_publications($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_assessment_publications($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_assessment_publication($id)
	{
		return $this->get_parent()->retrieve_assessment_publication($id);
	}

	function count_assessment_publication_groups($condition)
	{
		return $this->get_parent()->count_assessment_publication_groups($condition);
	}

	function retrieve_assessment_publication_groups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_assessment_publication_groups($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_assessment_publication_group($id)
	{
		return $this->get_parent()->retrieve_assessment_publication_group($id);
	}

	function count_assessment_publication_users($condition)
	{
		return $this->get_parent()->count_assessment_publication_users($condition);
	}

	function retrieve_assessment_publication_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_assessment_publication_users($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_assessment_publication_user($id)
	{
		return $this->get_parent()->retrieve_assessment_publication_user($id);
	}

	// Url Creation

	function get_create_assessment_publication_url()
	{
		return $this->get_parent()->get_create_assessment_publication_url();
	}

	function get_update_assessment_publication_url($assessment_publication)
	{
		return $this->get_parent()->get_update_assessment_publication_url($assessment_publication);
	}

 	function get_delete_assessment_publication_url($assessment_publication)
	{
		return $this->get_parent()->get_delete_assessment_publication_url($assessment_publication);
	}

	function get_browse_assessment_publications_url()
	{
		return $this->get_parent()->get_browse_assessment_publications_url();
	}
}
?>