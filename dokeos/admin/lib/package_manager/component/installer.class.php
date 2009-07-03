<?php
/**
 * @package admin
 * @subpackage package_manager
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/remote_package_browser/remote_package_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer.class.php';

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
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PackageInstallation')));
		$trail->add_help('administration install');

		if (!AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$this->display_header($trail);
		$installer = new PackageInstaller();
		if (!$installer->run())
		{
		    echo 'Doh';
		}
		else
		{
		    echo 'Woohoo';
		}
		$this->display_footer();
	}
}
?>