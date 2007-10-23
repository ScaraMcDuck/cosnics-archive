<?php

/**
 * @package users.lib
 */
require_once dirname(__FILE__).'/../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../common/authentication/authentication.class.php';
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
	 * userdatamanager
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
	 * retrieves the next user id.
	 */
	abstract function get_next_user_id();

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
	 * Deletes all users from the persistant storage
	 */
	abstract function delete_all_users();

	/**
	 * Updates the given user in persistent storage.
	 * @param User $user The user.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_user($user);

	/**
	 * Updates the given user quota in persistent storage.
	 * @param object $user_quota
		 */
	abstract function update_user_quota($user_quota);

	/**
	 * Makes the given User persistent.
	 * @param User $user The user.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_user($user);

	/**
	 * Creates a storage unit.
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name, $properties, $indexes);

	/**
	 * Retrieves a user.
	 * @param $id the user ID to retrieve the info from
	 */
	abstract function retrieve_user($id);
	/**
	 * Logs a user in to the platform
	 * @param string $username
	 * @param string $password
	 */
	public function login($username, $password = null)
	{
			// If username is available, try to login
	if (!$this->is_username_available($username))
		{
			$user = $this->retrieve_user_by_username($username);
			$authentication_method = $user->get_auth_source();
			$authentication = Authentication::factory($authentication_method);
			if ($authentication->check_login($user, $username, $password))
			{
				return $user;
			}
			return null;
		}
		// If username is not available, check if an authentication method is able to register
		// the user in the platform
		else
		{
			$authentication_dir = dir(dirname(__FILE__).'/../../common/authentication/');
			while (false !== ($authentication_method = $authentication_dir->read()))
			{
				if(strpos($authentication_method,'.') === false && is_dir($authentication_dir->path.'/'.$authentication_method))
				{
					$authentication_class_file = $authentication_dir->path.'/'.$authentication_method.'/'.$authentication_method.'authentication.class.php';
					$authentication_class = ucfirst($authentication_method).'Authentication';
					require_once $authentication_class_file;
					$authentication = new $authentication_class;
					if($authentication->can_register_new_user())
					{
						if($authentication->register_new_user($username,$password))
						{
							$authentication_dir->close();
							return $this->retrieve_user_by_username($username);
						}
					}
				}
			}
			$authentication_dir->close();
			return null;
		}
	}
	/**
	 * Logs the user out of the system
	 */
	public function logout()
	{
		$user = $this->retrieve_user(api_get_user_id());
		$authentication_method = $user->get_auth_source();
		$authentication_class_file = dirname(__FILE__).'/../../common/authentication/'.$authentication_method.'/'.$authentication_method.'authentication.class.php';
		$authentication_class = ucfirst($authentication_method).'Authentication';
		require_once $authentication_class_file;
		$authentication = new $authentication_class;
		if ($authentication->logout($user))
		{
			return true;
		}
		return false;
	}
	/**
	 * Retrieves a user by his or her username.
	 * @param $username the username to retrieve the info from
	 */
	abstract function retrieve_user_by_username($username);

	/**
	 * Retrieves users.
	 */
	abstract function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);

	/**
	 * Counts the amount of users currently in the database
	 * @param $conditions optional conditions
	 */
	abstract function count_users($conditions = null);

	/**
	 * Retrieves the version type quota
	 * @param $user The user
	 * @param $type quota type
	 */
	abstract function retrieve_version_type_quota($user, $type);

	/**
	 * Checks whether the user is allowed to be deleted
	 * Unfinished.
	 */
	function user_deletion_allowed($user)
	{
		// TODO: Check if the user can be deleted (fe: can an admin delete another admin etc)
		return true;
	}

	/**
	 * Checks whether this username is available in the database
	 */
	abstract function is_username_available($username, $user_id = null);
}
?>