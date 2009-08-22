<?php
require_once Path :: get_common_path() . 'sub_manager_component.class.php';

class GroupRightManagerComponent extends SubManagerComponent
{
	function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property, $order_direction);
	}

	function count_locations($conditions = null)
	{
		return $this->get_parent()->count_locations($conditions);
	}

	function retrieve_location($location_id)
	{
		return $this->get_parent()->retrieve_location($location_id);
	}

	function retrieve_group_right_location($right_id, $group_id, $location_id)
	{
		return $this->get_parent()->retrieve_group_right_location($right_id, $group_id, $location_id);
	}
	
	function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_groups($condition, $offset, $count, $order_property, $order_direction);
	}

	function count_groups($conditions = null)
	{
		return $this->get_parent()->count_groups($conditions);
	}
}
?>