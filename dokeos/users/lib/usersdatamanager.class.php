<?php
/**
 * $Id: repositorydatamanager.class.php 11718 2007-03-27 09:52:32Z Scara84 $
 * @package repository
 */
require_once dirname(__FILE__).'/../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../repository/lib/repositorydatamanager.class.php';
/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */
abstract class UsersDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Associative array that maps learning object types to their
	 * corresponding array of property names.
	 */
	private $typeProperties;

	/**
	 * Array which contains the registered applications running on top of this
	 * repositorydatamanager
	 */
	private $applications;

	/**
	 * Constructor.
	 */
	protected function UsersDataManager()
	{
		$this->initialize();
	}
	
	/**
	 * Initializes the data manager.
	 */
	abstract function initialize();

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return UsersDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'UsersDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	/**
	 * Deletes the given user from the persistant storage
	 * @param User $user The user.
	 */
	abstract function delete_user($user);
	
	/**
	 * Updates the given user in persistent storage.
	 * @param User $user The user.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_user($user);
	
	/**
	 * Makes the given User persistent.
	 * @param User $user The user.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_user($user);
	
	/**
	 * Creates a storage unit
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name,$properties,$indexes);
	
	abstract function retrieve_user($id);
	
	abstract function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
	abstract function count_users($conditions = null);
	
	function user_deletion_allowed($user)
	{
		// TODO: Check if the user can be deleted (fe: can an admin delete another admin etc)
		return true;
	}
	
	abstract function is_username_available($username, $user_id);
}
?>