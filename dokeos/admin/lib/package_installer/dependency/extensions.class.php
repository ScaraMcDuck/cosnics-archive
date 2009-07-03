<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_dependency.class.php';

class PackageInstallerExtensionsDependency extends PackageInstallerDependency
{
    function check($dependency)
    {
        return extension_loaded($dependency['id']);
    }
}
?>