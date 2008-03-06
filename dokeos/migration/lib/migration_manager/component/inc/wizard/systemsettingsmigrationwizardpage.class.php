<?php

/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../import.class.php'; 
/**
 * Class for user migration execution
 * @author Sven Vanpoucke
 */
class SystemSettingsMigrationWizardPage extends MigrationWizardPage
{
	private $logfile;
	private $mgdm;
	private $old_system;
	
	private $failed_elements;
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('System_Settings_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{
		$message = Translation :: get_lang('System_Settings_info');
		
		for($i=0; $i<2; $i++)
		{
			if(count($this->failed_elements[$i]) > 0)
				$message = $message . '<br / ><br />' . 
					$this->get_failed_message($i) . ' (' .
					Translation :: get_lang('Dont_forget') . ')';
			
			foreach($this->failed_elements[$i] as $felement)
			{
				$message = $message . '<br />' . $felement;
			}
		}
		
		return $message;
	}
	
	function get_failed_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('System_Settings_failed'); 
			case 1: return Translation :: get_lang('System_Announcements_failed'); 
			default: return Translation :: get_lang('System_Settings_failed'); 
		}
	}
	
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
	
	function perform()
	{
		$exportvalues = $this->controller->exportValues();
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('system_settings.txt');
		$this->logfile->set_start_time();
		
		//Create temporary tables, create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		//Migrate system settings
		$this->migrate_system_settings();
		
		//Migrate system announcements
		$this->migrate_system_announcements();
		
		//Close the logfile
		$this->logfile->write_all_messages();
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
	}
	
	/**
	 * Migrate course categories
	 */
	function migrate_system_settings()
	{
		$this->logfile->add_message('Starting migration system settings');
		
		$systemsettingsclass =  Import :: factory($this->old_system, 'settingcurrent');
		$systemsettings = array();
		$systemsettings = $systemsettingsclass->get_all_current_settings($this->mgdm);
		
		foreach($systemsettings as $systemsetting)
		{
			if($systemsetting->is_valid_current_setting())
			{
				$lcms_admin_setting = $systemsetting->convert_to_new_admin_setting();
				$this->logfile->add_message('System setting added ( ID: ' . 
					$lcms_admin_setting->get_id() . ' )');
			}
			else
			{
				$message = 'System setting is not valid ( ID: ' . $systemsetting->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
		}
		
		$this->logfile->add_message('System setting migrated');
	}
	
	/**
	 * Migrate System Announcements
	 */
	function migrate_system_announcements()
	{
		$this->logfile->add_message('Starting migration system announcements');
		
		$systemannouncementsclass =  Import :: factory($this->old_system, 'systemannouncement');
		$systemannouncements = array();
		$systemannouncements = $systemannouncementsclass->get_all_system_announcements($this->mgdm);;
		
		foreach($systemannouncements as $systemannouncement)
		{
			if($systemannouncement->is_valid_system_announcement())
			{
				$lcms_system_announcement = $systemannouncement->convert_to_new_system_announcement(1);
				$this->logfile->add_message('System announcement added ( ID: ' . 
					$lcms_system_announcement->get_id() . ' )');
			}
			else
			{
				$message = 'System announecment is not valid ( ID: ' . $systemannouncement->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[1][] = $message;
			}
		}
		
		$this->logfile->add_message('System announcements migrated');
	}

}
?>