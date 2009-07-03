<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_dependency.class.php';

class PackageInstallerServerDependency extends PackageInstallerDependency
{
    function check($dependency)
    {
        switch($dependency['id'])
        {
            case 'php' :
                return $this->version_compare($dependency['version']['type'], $dependency['version']['_content'], phpversion());
                break;
            default :
                return true;
        }
    }
}
?>