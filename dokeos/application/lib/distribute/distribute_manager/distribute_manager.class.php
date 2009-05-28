<?php
/**
 * @package application.lib.distribute.distribute_manager
 */
require_once dirname(__FILE__).'/distribute_manager_component.class.php';
require_once dirname(__FILE__).'/../distribute_data_manager.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';
//require_once dirname(__FILE__).'/component/distribute_publication_browser/distribute_publication_browser_table.class.php';

/**
 * A distribute manager
 * @author Hans De Bisschop
 */
 class DistributeManager extends WebApplication
 {
 	const APPLICATION_NAME = 'distribute';

	const ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS = 'browse';
	const ACTION_DISTRIBUTE_ANNOUNCEMENT = 'distribute';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function DistributeManager($user = null)
    {
    	parent :: __construct($user);
    	//$this->parse_input_from_table();
    }

    /**
	 * Run this distribute manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
		    case self :: ACTION_DISTRIBUTE_ANNOUNCEMENT :
		        $component = DistributeManagerComponent :: factory('Distributor', $this);
		        break;
			default :
				$this->set_action(self :: ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS);
				$component = DistributeManagerComponent :: factory('Browser', $this);

		}
		$component->run();
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving

	function count_distribute_publications($condition)
	{
		return DistributeDataManager :: get_instance()->count_distribute_publications($condition);
	}

	function retrieve_distribute_publications($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return DistributeDataManager :: get_instance()->retrieve_distribute_publications($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_distribute_publication($id)
	{
		return DistributeDataManager :: get_instance()->retrieve_distribute_publication($id);
	}

	// Url Creation

	function get_create_distribute_publication_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_DISTRIBUTE_PUBLICATION));
	}

	function get_update_distribute_publication_url($distribute_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_DISTRIBUTE_PUBLICATION,
								    self :: PARAM_DISTRIBUTE_PUBLICATION => $distribute_publication->get_id()));
	}

 	function get_delete_distribute_publication_url($distribute_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_DISTRIBUTE_PUBLICATION,
								    self :: PARAM_DISTRIBUTE_PUBLICATION => $distribute_publication->get_id()));
	}

	function get_browse_distribute_publications_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_DISTRIBUTE_PUBLICATIONS));
	}

	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}

	// Dummy Methods which are needed because we don't work with learning objects
	function learning_object_is_published($object_id)
	{
	}

	function any_learning_object_is_published($object_ids)
	{
	}

	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
	}

	function get_learning_object_publication_attribute($object_id)
	{

	}

	function count_publication_attributes($type = null, $condition = null)
	{

	}

	function delete_learning_object_publications($object_id)
	{

	}

	function update_learning_object_publication_id($publication_attr)
	{

	}

	function get_learning_object_publication_locations($learning_object)
	{

	}

	function publish_learning_object($learning_object, $location)
	{

	}
}
?>