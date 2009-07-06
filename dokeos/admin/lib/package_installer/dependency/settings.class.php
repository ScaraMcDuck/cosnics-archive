<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_dependency.class.php';

class PackageInstallerSettingsDependency extends PackageInstallerDependency
{

    function check($dependency)
    {
        $setting = ini_get($dependency['id']);
        $message = Translation :: get('DependencyCheckSetting') . ': ' . $dependency['id'] . '. ' . Translation :: get('Expecting') . ': ' . $dependency['value']['_content'] . ' ' . Translation :: get('Found') . ': ' . $setting;
        
        $this->add_message($message);
        return $this->compare($dependency['value']['type'], $dependency['value']['_content'], $setting);
    }
}
?>