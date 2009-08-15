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
}
?>