<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';
require_once Path :: get_library_path() . 'installer.class.php';

class PackageInstallerApplicationType extends PackageInstallerType
{
    function install()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();

        $application_path = Path :: get_application_path() . 'lib/';
        $application_full_path = $application_path . $application_name;

        //Filesystem :: move_file($source->get_package_folder(), $application_full_path);

		$installer = Installer :: factory($application_name, array());
		$result = $installer->install();
		dump($result);
		$result = $installer->post_process();
		dump($result);
		exit;

        $this->cleanup();
    }
}
?>