<?php

/**
 *	This is a skeleton for the data_manager_functions of a classgroup
 *
 *	@author Sven Vanpoucke
 */
interface DataManagerFunctions
{
	public function initialize();
	
	public function get_next_classgroup_id();
	
	public function delete_classgroup($classgroup);
	
	public function delete_classgroup_rel_user($classgroupreluser);
	
	public function update_classgroup($classgroup);
	
	public function create_classgroup($classgroup);
	
	public function create_classgroup_rel_user($classgroupreluser);

	public function create_storage_unit($name,$properties,$indexes);
	
	public function count_classgroups($conditions = null);
	
	public function count_classgroup_rel_users($conditions = null);
	
	public function retrieve_classgroup($id);
	
	public function truncate_classgroup($id);
	
	public function retrieve_classgroups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	public function retrieve_classgroup_rel_user($user_id, $group_id);
	
	public function retrieve_classgroup_rel_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
}
?>	