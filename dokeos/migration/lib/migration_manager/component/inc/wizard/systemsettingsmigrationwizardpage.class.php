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
class CoursesMigrationWizardPage extends MigrationWizardPage
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
		
		for($i=0; $i<1; $i++)
		{
			if(count($this->failed_elements[$i]) > 0)
				$message = $message . '<br / ><br />' . 
					$this->get_failed_message() . ' (' .
					Translation :: get_lang('Dont_forget') . ')';
			
			foreach($this->failed_elements[$i] as $felement)
			{
				$message = $message . '<br />' . $felement;
			}
		}
		
		return $message;
	}
	
	function get_failed_message()
	{
		return Translation :: get_lang('System_Settings_failed'); 
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
		
		//Migrate course categories
		$this->migrate_system_settings();
		
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
		
		$systemsettingsclass =  Import :: factory($this->old_system, 'systemannouncement');
		$systemsettings = array();
		$systemsettings = $systemsettingsclass->get_all_system_settings($this->mgdm);
		
		foreach($systemsettings as $systemsetting)
		{
			if($systemsetting->is_valid_system_setting())
			{
				$lcms_admin_setting = $systemsetting->convert_to_new_system_setting();
				$this->logfile->add_message('System setting added ( CODE: ' . 
					$lcms_admin_setting->get_code() . ' )');
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

}
?>