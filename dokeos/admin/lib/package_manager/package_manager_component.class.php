<?php
require_once Path :: get_common_path() . 'sub_manager_component.class.php';

class PackageManagerComponent extends SubManagerComponent
{
    function retrieve_registration($id)
    {
        return $this->get_parent()->retrieve_registration($id);
    }

    function retrieve_registrations($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
    {
        return $this->get_parent()->retrieve_registrations($condition, $orderBy, $orderDir, $offset, $maxObjects);
    }

    function count_registrations($condition = null)
    {
        return $this->get_parent()->count_registrations($condition);
    }

    function get_registration_activation_url($registration)
    {
        return $this->get_parent()->get_registration_activation_url($registration);
    }

    function get_registration_deactivation_url($registration)
    {
        return $this->get_parent()->get_registration_deactivation_url($registration);
    }

    function get_remote_package_installation_url($remote_package)
    {
        return $this->get_parent()->get_remote_package_installation_url($remote_package);
    }

    /**
     * @see AdminManager :: count_remote_packages()
     */
    function count_remote_packages($condition = null)
    {
        return $this->get_parent()->count_remote_packages($condition);
    }

    /**
     * @see AdminManager :: retrieve_remote_package()
     */
    function retrieve_remote_package($id)
    {
        return $this->get_parent()->retrieve_remote_package($id);
    }

    /**
     * @see AdminManager :: retrieve_remote_packages()
     */
    function retrieve_remote_packages($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
    {
        return $this->get_parent()->retrieve_remote_packages($condition, $orderBy, $orderDir, $offset, $maxObjects);
    }
}
?>