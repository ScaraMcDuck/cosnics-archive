<?php
/**
 * $Id: personal_calendar_installer.class.php 12686 2007-07-03 11:32:57Z bmol $
 * @package users.install
 */
require_once dirname(__FILE__).'/../lib/admindatamanager.class.php';
require_once dirname(__FILE__).'/../lib/language.class.php';
require_once dirname(__FILE__).'/../lib/setting.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class AdminInstaller extends Installer
{
	private $adm;
	private $values;
	/**
	 * Constructor
	 */
    function AdminInstaller($values)
    {
    	$this->values = $values;
    	$this->adm = AdminDataManager :: get_instance();
    }
	/**
	 * Runs the install-script.
	 */
	function install()
	{
		$dir = dirname(__FILE__);
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				if (!$this->create_storage_unit($file))
				{
					return array('success' => false, 'message' => $this->retrieve_message());
				}
			}
		}
		
		// Add the default language entries in the database
		if (!$this->create_languages())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		else
		{
			$this->add_message(Translation :: get('DefaultLanguagesAdded'));
		}
		
		// Add the default settings to the database
		if (!$this->create_settings())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		else
		{
			$this->add_message(Translation :: get('DefaultSettingsAdded'));
		}
		
		$success_message = '<span style="color: green; font-weight: bold;">' . Translation :: get('ApplicationInstallSuccess') . '</span>';
		$this->add_message($success_message);
		return array('success' => true, 'message' => $this->retrieve_message());
	}

	/**
	 * Parses an XML file and sends the request to the database manager
	 * @param String $path
	 */
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$this->add_message(Translation :: get('StorageUnitCreation') . ': <em>'.$storage_unit_info['name'] . '</em>');
		if (!$this->adm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']))
		{
			$error_message = '<span style="color: red; font-weight: bold;">' . Translation :: get('StorageUnitCreationFailed') . ': <em>'.$storage_unit_info['name'] . '</em></span>';
			$this->add_message($error_message);
			$this->add_message(Translation :: get('ApplicationInstallFailed'));
			$this->add_message(Translation :: get('PlatformInstallFailed'));
			
			return false;
		}
		else
		{
			return true;
		}

	}
	
	function create_languages()
	{
		$lang_dutch = new Language();
		$lang_dutch->set_original_name('Nederlands');
		$lang_dutch->set_english_name('Dutch');
		$lang_dutch->set_isocode('nl');
		$lang_dutch->set_folder('dutch');
		$lang_dutch->set_available('1');
		
		if (!$lang_dutch->create())
		{
			return false;
		}
		
		$lang_english = new Language();
		$lang_english->set_original_name('English');
		$lang_english->set_english_name('English');
		$lang_english->set_isocode('en');
		$lang_english->set_folder('english');
		$lang_english->set_available('1');
		
		if (!$lang_english->create())
		{
			return false;
		}
		
		return true;
	}
	
	function create_settings()
	{
		$values = $this->values;
		
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
		
		$settings[] = array('admin', 'stylesheets', 'dokeos');
		
		$settings[] = array('admin', 'allow_password_retrieval', $values['encrypt_password']);
		$settings[] = array('admin', 'allow_registration', $values['self_reg']);
		
		foreach ($settings as $setting)
		{
			$setting_object = new Setting();
			$setting_object->set_application($setting[0]);
			$setting_object->set_variable($setting[1]);
			$setting_object->set_value($setting[2]);
			
			if (!$setting_object->create())
			{
				return false;
			}
		}
		
		return true;
	}
}
?>