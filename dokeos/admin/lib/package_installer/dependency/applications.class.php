<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_dependency.class.php';

class PackageInstallerApplicationsDependency extends PackageInstallerDependency
{
    function check($dependency)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $dependency['id']);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
        $condition = new AndCondition($conditions);

        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition, array(), array(), 0, 1);

        if ($registrations->size() === 0)
        {
            return false;
        }
        else
        {
            $registration = $registrations->next_result();
            if (!$registration->is_active())
            {
                return false;
            }
            else
            {
                $application_version = $this->version_compare($dependency['version']['type'], $dependency['version']['_content'], $registration->get_version());
                if (!$application_version)
                {
                    return false;
                }
            }
        }

        return true;
    }
}
?>