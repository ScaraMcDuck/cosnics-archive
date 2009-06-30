<?php
/**
 * @package admin
 * @subpackage package_manager
 * @author Hans De Bisschop
 */
class PackageManagerInstallerComponent extends PackageManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES)), Translation :: get('PackageManager')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Install')));
		$trail->add_help('administration install');

		if (!AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$this->display_header($trail);
		
		echo 'Install from: local filesystem (app and lo-folder combined with registrations), online resource (DK-planet server ?) as well as uploaded archives.';

		$this->display_footer();
	}
}
?>