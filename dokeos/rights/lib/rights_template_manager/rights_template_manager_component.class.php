<?php
require_once Path :: get_common_path() . 'sub_manager_component.class.php';

class RightsTemplateManagerComponent extends SubManagerComponent
{
	function retrieve_rights_templates($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_rights_templates($condition, $offset, $count, $order_property, $order_direction);
	}

	function count_rights_templates($conditions = null)
	{
		return $this->get_parent()->count_rights_templates($conditions);
	}

	function get_rights_template_editing_url($rights_template)
	{
		return $this->get_parent()->get_rights_template_editing_url($rights_template);
	}

	function get_rights_template_deleting_url($rights_template)
	{
		return $this->get_parent()->get_rights_template_deleting_url($rights_template);
	}

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

	function retrieve_rights_template_right_location($right, $rights_template_id, $location_id)
	{
		return $this->get_parent()->retrieve_rights_template_right_location($right, $rights_template_id, $location_id);
	}

	function retrieve_rights_template($id)
	{
		return $this->get_parent()->retrieve_rights_template($id);
	}
}
?>