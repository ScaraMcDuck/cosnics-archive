<?php
require_once Path :: get_admin_path() . 'lib/package_manager/package_manager.class.php';
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_source.class.php';
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';

class PackageInstaller
{
    private $source;

    function PackageInstaller()
    {
        $this->source = Request :: get(PackageManager :: PARAM_INSTALL_TYPE);
    }

    function run()
    {
        $installer_source = PackageInstallerSource :: factory($this->source);
        if (!$installer_source->process())
        {
            return false;
        }
        else
        {
            $attributes = $installer_source->get_attributes();
            $package = PackageInstallerType :: factory($attributes->get_section(), $installer_source);
            if (!$package->install())
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }
}
?>