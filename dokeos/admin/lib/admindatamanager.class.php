<?php
/**
 * @package admin.lib
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../users/lib/usermanager/usermanager.class.php';
require_once dirname(__FILE__).'/../../repository/lib/repository_manager/repositorymanager.class.php';

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
	 * Loads the applications installed on the system and uses the function
	 * register_application to register them.
	 */
	private function load_applications()
	{
		$applications = Application::load_all();
		foreach($applications as $index => $application)
		{
			$this->register_application($application);
		}
	}

	/**
	 * Registers an application with this admin datamanager.
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
		//$info[] = array('application' => array('name' => get_lang('UserRolesRights'), 'class' => 'user_roles_rights'), 'links' => array(array('name' => get_lang('ManageRoles'), 'action' => 'manage', 'url' => '/LCMS/main/admin/manage_roles.php'), array('name' => get_lang('RolesRightsOverview'), 'action' => 'list', 'url' => '/LCMS/main/admin/roles_rights_overview.php')));

		// 3. Classes of Users
		//$info[] = array('application' => array('name' => get_lang('ClassesOfUsers'), 'class' => 'user_classes'), 'links' => array(array('name' => get_lang('ClassList'), 'action' => 'list', 'url' => '/LCMS/main/admin/class_list.php'), array('name' => get_lang('AddClasses'), 'action' => 'add', 'url' => '/LCMS/main/admin/class_add.php'), array('name' => get_lang('ImportClasses'), 'action' => 'import', 'url' => '/LCMS/main/admin/class_import.php')));

		// 4. Platform
		// Deleted from actions: , array('name' => get_lang('ConfigureHomepage'), 'action' => 'home', 'url' => '/LCMS/main/admin/configure_homepage.php')
		$old_admin_url = api_get_path(WEB_CODE_PATH);
		$info[] = array('application' => array('name' => get_lang('Platform'), 'class' => 'platform'), 'links' => array(array('name' => get_lang('DokeosConfiguration'), 'action' => 'manage', 'url' => $old_admin_url.'admin/settings.php'), array('name' => get_lang('SystemAnnouncements'), 'action' => 'system', 'url' => 'index_admin.php?'.Admin::PARAM_ACTION.'='.Admin::ACTION_SYSTEM_ANNOUNCEMENTS), array('name' => get_lang('Languages'), 'action' => 'language', 'url' => $old_admin_url.'admin/languages.php')));

		// 5. Repository
		$repository_manager = new RepositoryManager($user);
		$info[] = $repository_manager->get_application_platform_admin_links();

		// Secondly the links for the plugin applications running on top of the essential Dokeos components
		$applications = $this->get_registered_applications();
		foreach($applications as $index => $application_name)
		{
			$application = Application::factory($application_name);
			$links = $application->get_application_platform_admin_links();
			if ($links['application']['name'])
			{
				$links['application']['name'] = get_lang('App'.Application::application_to_class($links['application']['name']));
				$info[] = $links;
			}
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
