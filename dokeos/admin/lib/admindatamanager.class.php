<?php
/**
 * @package admin
 */
require_once dirname(__FILE__).'/../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../users/lib/usermanager/usermanager.class.php';

abstract class AdminDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Array which contains the registered applications running on top of this
	 * admindatamanager
	 */
	private $applications;

	/**
	 * Constructor.
	 */
	protected function AdminDataManager()
	{
		$this->applications = array();
		$this->load_applications();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return RepositoryDataManager The data manager.
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
	 * Loads the applications installed on the system. Applications are classes
	 * in the /application/lib subdirectory. Each application is a directory,
	 * which in its turn contains a class file named after the application. For
	 * instance, the weblcms application is the class Weblcms, defined in
	 * /application/lib/weblcms/weblcms.class.php. Applications must extend the
	 * Application class.
	 */
	private function load_applications()
	{
		$path = dirname(__FILE__).'/../../application/lib';
		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				$toolPath = $path.'/'. $file .'/'.$file.'_manager';
				if (is_dir($toolPath) && self :: is_application_name($file))
				{
					require_once $toolPath.'/'.$file.'.class.php';
					$this->register_application($file);
				}
			}
			closedir($handle);
		}
		else
		{
			die('Failed to load applications');
		}
	}
	
	/**
	 * Registers an application with this repository datamanager.
	 * @param string $application The application name.
	 */
	function register_application($application)
	{
		if (in_array($application, $this->applications))
		{
			die('Application already registered: '.$application);
		}
		$this->applications[] = $application;
	}
	
	/**
	 * Converts an application name to the corresponding class name.
	 * @param string $application The application name.
	 * @return string The class name.
	 */
	static function application_to_class($application)
	{
		return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $application));
	}

	/**
	 * Converts an application class name to the corresponding application name.
	 * @param string $class The class name.
	 * @return string The application name.
	 */
	static function class_to_application($class)
	{
		return preg_replace(array ('/^([A-Z])/e', '/([A-Z])/e'), array ('strtolower(\1)', '"_".strtolower(\1)'), $class);
	}

	/**
	 * Determines whether or not the given name is a valid application name.
	 * @param string $name The name to evaluate.
	 * @return True if the name is a valid application name, false otherwise.
	 */
	static function is_application_name($name)
	{
		return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
	}
	
	function get_application_platform_admin_links()
	{
		$info = array();
		
		// First we get the links for the essential Dokeos components
		
		// 1. UserManager
		$user_manager = new UserManager(api_get_user_id());
		$info[] = $user_manager->get_application_platform_admin_links();
		
		// 2. UserRolesRights
		$info[] = array('application' => array('name' => get_lang('UserRolesRights'), 'class' => 'user_roles_rights'), 'links' => array(array('name' => get_lang('ManageRoles'), 'action' => 'manage', 'url' => '/LCMS/main/admin/manage_roles.php'), array('name' => get_lang('RolesRightsOverview'), 'action' => 'list', 'url' => '/LCMS/main/admin/roles_rights_overview.php')));
		
		// 3. Classes of Users
		$info[] = array('application' => array('name' => get_lang('ClassesOfUsers'), 'class' => 'user_classes'), 'links' => array(array('name' => get_lang('ClassList'), 'action' => 'list', 'url' => '/LCMS/main/admin/class_list.php'), array('name' => get_lang('AddClasses'), 'action' => 'add', 'url' => '/LCMS/main/admin/class_add.php'), array('name' => get_lang('ImportClasses'), 'action' => 'import', 'url' => '/LCMS/main/admin/class_import.php')));
		
		// 4. Platform
		$info[] = array('application' => array('name' => get_lang('Platform'), 'class' => 'platform'), 'links' => array(array('name' => get_lang('DokeosConfiguration'), 'action' => 'manage', 'url' => '/LCMS/main/admin/settings.php'), array('name' => get_lang('SystemAnnouncements'), 'action' => 'system', 'url' => '/LCMS/main/admin/system_announcements.php'), array('name' => get_lang('Languages'), 'action' => 'language', 'url' => '/LCMS/main/admin/languages.php'), array('name' => get_lang('ConfigureHomepage'), 'action' => 'home', 'url' => '/LCMS/main/admin/configure_homepage.php')	));		
		
		// Secondly the links for the plugin applications running on top of the essential Dokeos components
		$applications = $this->get_registered_applications();
		foreach($applications as $index => $application_name)
		{
			$application_class = self::application_to_class($application_name);
			$application = new $application_class;
			$links = $application->get_application_platform_admin_links();
			$links['application']['name'] = $this->application_to_class($links['application']['name']);
			$info[] = $links; 
		}

		return $info;
	}
	
	/**
	 * Returns the names of the applications known to this
	 * admin.
	 * @return array The applications.
	 */
	function get_registered_applications()
	{
		return $this->applications;
	}
}
?>
