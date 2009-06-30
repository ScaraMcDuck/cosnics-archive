<?php
/**
 * @package admin
 * @subpackage package_manager
 * @author Hans De Bisschop
 */
require_once Path :: get_common_path() . 'sub_manager.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/package_manager_component.class.php';

class PackageManager extends SubManager
{
    const PARAM_PACKAGE_ACTION = 'action';
    const PARAM_REGISTRATION = 'registration';
    const PARAM_ACTIVATE_SELECTED = 'activate';
    const PARAM_DEACTIVATE_SELECTED = 'deactivate';

    const ACTION_BROWSE_PACKAGES = 'browse';
    const ACTION_ACTIVATE_PACKAGE = 'activate';
    const ACTION_DEACTIVATE_PACKAGE = 'deactivate';
    const ACTION_INSTALL_PACKAGE = 'install';
    const ACTION_CYCLE_PACKAGE_ACTIVATION = 'cycle';

    function PackageManager($admin_manager)
    {
        parent :: __construct($admin_manager);

        $package_action = Request :: get(self :: PARAM_PACKAGE_ACTION);
        if ($package_action)
        {
            $this->set_parameter(self :: PARAM_PACKAGE_ACTION, $package_action);
        }
    }

    function run()
    {
        $package_action = $this->get_parameter(self :: PARAM_PACKAGE_ACTION);

        switch ($package_action)
        {
            case self :: ACTION_BROWSE_PACKAGES :
                $component = PackageManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_ACTIVATE_PACKAGE :
                $component = PackageManagerComponent :: factory('Activator', $this);
                break;
            case self :: ACTION_DEACTIVATE_PACKAGE :
                $component = PackageManagerComponent :: factory('Deactivator', $this);
                break;
            case self :: ACTION_INSTALL_PACKAGE :
                $component = PackageManagerComponent :: factory('Installer', $this);
                break;
            case self :: ACTION_CYCLE_PACKAGE_ACTIVATION :
                $component = PackageManagerComponent :: factory('Cycler', $this);
                break;
            default :
                $component = PackageManagerComponent :: factory('Browser', $this);
        }

        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_admin_path() . 'lib/package_manager/component/';
    }

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
        return $this->get_url(array(self :: PARAM_PACKAGE_ACTION => self :: ACTION_ACTIVATE_PACKAGE, self :: PARAM_REGISTRATION => $registration->get_id()));
    }

    function get_registration_deactivation_url($registration)
    {
        return $this->get_url(array(self :: PARAM_PACKAGE_ACTION => self :: ACTION_DEACTIVATE_PACKAGE, self :: PARAM_REGISTRATION => $registration->get_id()));
    }
}
?>