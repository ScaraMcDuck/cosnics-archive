<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_source.class.php';

class PackageInstallerLocalSource extends PackageInstallerSource
{
	function get_archive()
	{
		
	}
	
    function process()
    {
    	$package_section = Request :: get(PackageManager :: PARAM_SECTION);
		$package_code = Request :: get(PackageManager :: PARAM_PACKAGE);
		
		$package = new RemotePackage();
		$package->set_section($package_section);
		$package->set_code($package_code);
		$package->set_name(DokeosUtilities :: underscores_to_camelcase_with_spaces($package_code));
		$package->set_dependencies(serialize(array()));
		
		$this->set_attributes($package);
		$this->get_parent()->add_message(Translation :: get('LocalPackageProcessed'));
		return true;
    }
}
?>