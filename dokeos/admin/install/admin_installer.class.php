<?php
/**
 * $Id: personal_calendar_installer.class.php 12686 2007-07-03 11:32:57Z bmol $
 * @package users.install
 */
require_once dirname(__FILE__) . '/../lib/admin_data_manager.class.php';
require_once dirname(__FILE__) . '/../lib/language.class.php';
require_once dirname(__FILE__) . '/../lib/setting.class.php';
require_once Path :: get_library_path() . 'installer.class.php';
require_once Path :: get_library_path() . 'filesystem/filesystem.class.php';
/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class AdminInstaller extends Installer
{

    /**
     * Constructor
     */
    function AdminInstaller($values)
    {
        parent :: __construct($values, AdminDataManager :: get_instance());
    }

    /**
     * Runs the install-script.
     */
    function install_extra()
    {
        
        // Add the default language entries in the database
        if (! $this->create_languages())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('DefaultLanguagesAdded'));
        }
        
        // Update the default settings to the database
        if (! $this->update_settings())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('DefaultSettingsAdded'));
        }
        
        return true;
    }

    function create_languages()
    {
        $lang_dutch = new Language();
        $lang_dutch->set_original_name('Nederlands');
        $lang_dutch->set_english_name('Dutch');
        $lang_dutch->set_isocode('nl');
        $lang_dutch->set_folder('dutch');
        $lang_dutch->set_available('1');
        
        if (! $lang_dutch->create())
        {
            return false;
        }
        
        $lang_english = new Language();
        $lang_english->set_original_name('English');
        $lang_english->set_english_name('English');
        $lang_english->set_isocode('en');
        $lang_english->set_folder('english');
        $lang_english->set_available('1');
        
        if (! $lang_english->create())
        {
            return false;
        }
        
        return true;
    }

    function update_settings()
    {
        $values = $this->get_form_values();
        
        $settings = array();
        $settings[] = array('admin', 'site_name', $values['platform_name']);
        $settings[] = array('admin', 'server_type', 'production');
        $settings[] = array('admin', 'platform_language', $values['platform_language']);
        $settings[] = array('admin', 'version', '2.0');
        $settings[] = array('admin', 'theme', 'aqua');
        
        $settings[] = array('admin', 'institution', $values['organization_name']);
        $settings[] = array('admin', 'institution_url', $values['organization_url']);
        
        $settings[] = array('admin', 'show_administrator_data', 'true');
        $settings[] = array('admin', 'administrator_firstname', $values['admin_firstname']);
        $settings[] = array('admin', 'administrator_surname', $values['admin_surname']);
        $settings[] = array('admin', 'administrator_email', $values['admin_email']);
        $settings[] = array('admin', 'administrator_telephone', $values['admin_phone']);
        
        $settings[] = array('user', 'allow_password_retrieval', $values['encrypt_password']);
        $settings[] = array('user', 'allow_registration', $values['self_reg']);
        
        foreach ($settings as $setting)
        {
            $setting_object = AdminDataManager :: get_instance()->retrieve_setting_from_variable_name($setting[1], $setting[0]);
            $setting_object->set_application($setting[0]);
            $setting_object->set_variable($setting[1]);
            $setting_object->set_value($setting[2]);
            
            if (! $setting_object->update())
            {
                print_r($setting);
                return false;
            }
        }
        
        return true;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>