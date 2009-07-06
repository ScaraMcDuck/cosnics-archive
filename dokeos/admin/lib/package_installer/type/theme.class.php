<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';
require_once Path :: get_library_path() . 'installer.class.php';
require_once Path :: get_admin_path() . 'lib/registration.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

class PackageInstallerThemeType extends PackageInstallerType
{

    function install()
    {
        if ($this->verify_dependencies())
        {
            $this->get_parent()->installation_successful('dependencies', Translation :: get('ThemeDependenciesVerified'));
        }
        else
        {
            return $this->get_parent()->installation_failed('dependencies', Translation :: get('PackageDependenciesFailed'));
        }
        
        $this->cleanup();
        
        return true;
    }
}
?>