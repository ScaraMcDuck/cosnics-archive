<?php
/**
 * @package admin.lib
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_repository_path(). 'lib/repository_manager/repositorymanager.class.php';
require_once Path :: get_user_path(). 'lib/usermanager/usermanager.class.php';
require_once Path :: get_classgroup_path(). 'lib/classgroup_manager/classgroupmanager.class.php';
require_once Path :: get_tracking_path(). 'lib/tracking_manager/trackingmanager.class.php';

abstract class AdminDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	protected function AdminDataManager()
	{
		$this->initialize();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return AdminDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'AdminDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	/**
	 * Returns a list of actions available to the admin.
	 * @param User $user The current user.
	 * @return Array $info Contains all possible actions.
	 */
	function get_application_platform_admin_links($user)
	{
		$info = array();

		// First we get the links for the essential Dokeos components

		// 1. UserManager
		$user_manager = new UserManager($user->get_user_id());
		$info[] = $user_manager->get_application_platform_admin_links();

		// 2. UserRolesRights
		// 3. Classes of Users
		$classgroup_manager = new ClassGroupManager($user->get_user_id());
		$info[] = $classgroup_manager->get_application_platform_admin_links();		
		// 4. Platform
		
		// 5. Repository
		$repository_manager = new RepositoryManager($user);
		$info[] = $repository_manager->get_application_platform_admin_links();

		// 6. Tracking
		$tracking_manager = new TrackingManager($user);
		$info[] = $tracking_manager->get_application_platform_admin_links();

		// Secondly the links for the plugin applications running on top of the essential Dokeos components
		$applications = Application :: load_all();
		foreach($applications as $index => $application_name)
		{
			$application = Application::factory($application_name);
			$links = $application->get_application_platform_admin_links();
			if ($links['application']['name'])
			{
				$links['application']['name'] = Translation :: get(Application::application_to_class($links['application']['name']));
				$info[] = $links;
			}
		}

		return $info;
	}
	
	/**
	 * Creates a storage unit
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name,$properties,$indexes);
	
	abstract function get_next_setting_id();
	
	abstract function get_next_language_id();
	
	abstract function create_language($language);
	
	abstract function create_setting($setting);
	
	abstract function record_to_language($record);
	
	abstract function record_to_setting($record);

	abstract function retrieve_languages($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1);
	
	abstract function retrieve_settings($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1);
	
	abstract function retrieve_setting_from_variable_name($variable, $application = 'admin');
	
	abstract function retrieve_language_from_english_name($english_name);
}
?>
