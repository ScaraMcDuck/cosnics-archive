<?php
/**
 * @package application.lib.distribute
 * Basic functionality of a component to talk with the distribute application
 * @author Hans De Bisschop
 */
require_once Path :: get_application_path() . 'lib/web_application_component.class.php';

abstract class DistributeManagerComponent extends WebApplicationComponent
{
	/**
	 * Constructor
	 * @param Distribute $distribute The distribute which
	 * provides this component
	 */
	protected function DistributeManagerComponent($distribute)
	{
	    parent :: __construct($distribute);
	}

	//Data Retrieval

	function count_announcement_distributions($condition)
	{
		return $this->get_parent()->count_announcement_distributions($condition);
	}

	function retrieve_announcement_distributions($condition = null, $offset = null, $count = null, $order_property = array(), $order_direction = array())
	{
		return $this->get_parent()->retrieve_announcement_distributions($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_announcement_distribution($id)
	{
		return $this->get_parent()->retrieve_announcement_distribution($id);
	}

	// Url Creation

	function get_create_announcement_distribution_url()
	{
		return $this->get_parent()->get_create_announcement_distribution_url();
	}

	function get_update_announcement_distribution_url($announcement_distribution)
	{
		return $this->get_parent()->get_update_announcement_distribution_url($announcement_distribution);
	}

 	function get_delete_announcement_distribution_url($announcement_distribution)
	{
		return $this->get_parent()->get_delete_announcement_distribution_url($announcement_distribution);
	}

	function get_browse_announcement_distributions_url()
	{
		return $this->get_parent()->get_browse_announcement_distributions_url();
	}

	function get_announcement_distribution_viewing_url($announcement_distribution)
	{
		return $this->get_parent()->get_announcement_distribution_viewing_url($announcement_distribution);
	}
}
?>