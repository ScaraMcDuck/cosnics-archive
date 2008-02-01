<?php
/**
 * $Id: personal_calendar_installer.class.php 12686 2007-07-03 11:32:57Z bmol $
 * @package users.install
 */
require_once dirname(__FILE__).'/../lib/admindatamanager.class.php';
require_once dirname(__FILE__).'/../lib/language.class.php';
require_once dirname(__FILE__).'/../lib/setting.class.php';
require_once dirname(__FILE__).'/../../common/installer.class.php';
require_once dirname(__FILE__).'/../../common/filesystem/filesystem.class.php';
/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class AdminInstaller extends Installer
{
	private $adm;
	/**
	 * Constructor
	 */
    function AdminInstaller()
    {
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
			$this->add_message(get_lang('DefaultLanguagesAdded'));
		}
		
		// Add the default settings to the database
		if (!$this->create_settings())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		else
		{
			$this->add_message(get_lang('DefaultSettingsAdded'));
		}
		
		$success_message = '<span style="color: green; font-weight: bold;">' . get_lang('ApplicationInstallSuccess') . '</span>';
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
		$this->add_message(get_lang('StorageUnitCreation') . ': <em>'.$storage_unit_info['name'] . '</em>');
		if (!$this->adm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']))
		{
			$error_message = '<span style="color: red; font-weight: bold;">' . get_lang('StorageUnitCreationFailed') . ': <em>'.$storage_unit_info['name'] . '</em></span>';
			$this->add_message($error_message);
			$this->add_message(get_lang('ApplicationInstallFailed'));
			$this->add_message(get_lang('PlatformInstallFailed'));
			
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
		$settings = array();
		
		$setting = new Setting();
		$setting->set_application('admin');
		$setting->set_variable('site_name');
		$setting->set_value('Dokeos');
		$settings[] = $setting;
		
		$setting = new Setting();
		$setting->set_application('admin');
		$setting->set_variable('server_type');
		$setting->set_value('production');
		$settings[] = $setting;
		
		$setting = new Setting();
		$setting->set_application('admin');
		$setting->set_variable('platform_language');
		$setting->set_value('english');
		$settings[] = $setting;
		
		$setting = new Setting();
		$setting->set_application('admin');
		$setting->set_variable('institution');
		$setting->set_value('Dokeos Company');
		$settings[] = $setting;
		
		$setting = new Setting();
		$setting->set_application('admin');
		$setting->set_variable('institution_url');
		$setting->set_value('http://www.dokeos.com');
		$settings[] = $setting;
		
		$setting = new Setting();
		$setting->set_application('admin');
		$setting->set_variable('show_administrator_data');
		$setting->set_value('true');
		$settings[] = $setting;
		
		$setting = new Setting();
		$setting->set_application('admin');
		$setting->set_variable('administrator_email');
		$setting->set_value('info@dokeos.com');
		$settings[] = $setting;
		
		$setting = new Setting();
		$setting->set_application('admin');
		$setting->set_variable('administrator_surname');
		$setting->set_value('Admin');
		$settings[] = $setting;
		
		$setting = new Setting();
		$setting->set_application('admin');
		$setting->set_variable('administrator_firstname');
		$setting->set_value('Mr.');
		$settings[] = $setting;
		
		$setting = new Setting();
		$setting->set_application('admin');
		$setting->set_variable('stylesheets');
		$settings[] = $setting;
		
		foreach ($settings as $setting)
		{
			if (!$setting->create())
			{
				return false;
			}
		}
		
		return true;
	}
}
?>