<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_dependency.class.php';

class PackageInstallerSettingsDependency extends PackageInstallerDependency
{
    function check($dependency)
    {
        return $this->compare($dependency['value']['type'], $dependency['value']['_content'], ini_get($dependency['id']));
    }
}
?>