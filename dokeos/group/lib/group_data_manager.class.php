<?php
/**
 * $Id: repository_data_manager.class.php 11718 2007-03-27 09:52:32Z Scara84 $
 * @package repository
 */
require_once dirname(__FILE__).'/../../common/configuration/configuration.class.php';
require_once dirname(__FILE__).'/../../repository/lib/repository_data_manager.class.php';
/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */
abstract class GroupDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Array which contains the registered applications running on top of this
	 * repositorydatamanager
	 */
	private $applications;

	/**
	 * Constructor.
	 */
	protected function GroupDataManager()
	{
		$this->initialize();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return GroupsDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'GroupDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}
	
	abstract function initialize();
	
	abstract function get_next_group_id();
	
	abstract function delete_group($group);
	
	abstract function delete_group_rel_user($groupreluser);
	
	abstract function update_group($group);
	
	abstract function create_group($group);
	
	abstract function create_group_rel_user($groupreluser);

	abstract function create_storage_unit($name,$properties,$indexes);
	
	abstract function count_groups($conditions = null);
	
	abstract function count_group_rel_users($conditions = null);
	
	abstract function retrieve_group($id);
	
	abstract function truncate_group($id);
	
	abstract function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	abstract function retrieve_group_rel_user($user_id, $group_id);
	
	abstract function retrieve_group_rel_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	abstract function retrieve_user_groups($user_id);
	
	abstract function retrieve_group_roles($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null);
	
	abstract function delete_group_roles($condition);
	
	abstract function add_role_link($group, $role_id);
	
	abstract function delete_role_link($group, $role_id);
	
	abstract function update_role_links($group, $roles);
}
?>