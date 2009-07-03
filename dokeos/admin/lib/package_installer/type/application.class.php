<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';
require_once Path :: get_library_path() . 'installer.class.php';
require_once path :: get_menu_path() . 'lib/menu_data_manager.class.php';
require_once path :: get_menu_path() . 'lib/menu_item.class.php';

class PackageInstallerApplicationType extends PackageInstallerType
{
    function install()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();

        $application_path = Path :: get_application_path() . 'lib/';
        $application_full_path = $application_path . $application_name;

        //Filesystem :: move_file($source->get_package_folder(), $application_full_path);
        if ($this->verify_dependencies())
        {
            $installer = Installer :: factory($application_name, array());
    		$result = $installer->install();
    		$result = $installer->post_process();

    		if (!$this->set_version())
    		{
    		    return false;
    		}

            if (!$this->add_menu_item())
    		{
    		    return false;
    		}
        }
        else
        {
            return false;
        }

        $this->cleanup();

        return true;
    }

    function set_version()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();

        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $application_name);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
        $condition = new AndCondition($conditions);

        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition, array(), array(), 0, 1);
        $registration = $registrations->next_result();
        $registration->set_version($attributes->get_version());
        return $registration->update();
    }

    function add_menu_item()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();

		$menu_item = new MenuItem();
		$menu_item->set_title(Translation :: get(DokeosUtilities :: underscores_to_camelcase($application_name)));
		$menu_item->set_application($application_name);
		$menu_item->set_section($application_name);
		$menu_item->set_category(0);
		return $menu_item->create();
    }
}
?>