<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_source.class.php';

class PackageInstallerArchiveSource extends PackageInstallerSource
{
    function get_path()
    {
        return 'archive';
    }
}
?>