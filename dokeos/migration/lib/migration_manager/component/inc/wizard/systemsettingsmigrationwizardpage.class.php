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
	//private $logfile;
	//private $mgdm;
	//private $old_system;
	
	//private $failed_elements;
	//private $succes;
	//private $command_execute;
	
	/**
	 * Constructor creates a new SystemSettingsMigrationWizardPage
	 * @param string $page_name the page name
	 * @param $parent the parent of the controller
	 * @param bool $command_execute to see if the page is executed by commandline or webinterface
	 */
	function SystemSettingsMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
		$this->command_execute = $command_execute;
	}
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get('System_Settings_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{		
		for($i=0; $i<2; $i++)
		{
			$message = $message . '<br />' . $this->succes[$i] . ' ' . $this->get_message($i) . ' ' .
				Translation :: get('migrated');
			
			if(count($this->failed_elements[$i]) > 0)
				$message = $message . '<br / >' . count($this->failed_elements[$i]) . ' ' .
					 $this->get_message($i) . ' ' . Translation :: get('failed');
			
			foreach($this->failed_elements[$i] as $felement)
			{
				$message = $message . '<br />' . $felement ;
			}
			
			$message = $message . '<br />';
		}
		
		$message = $message . '<br />' . Translation :: get('Dont_forget');
		
		return $message;
	}
	
	/**
	 * Retrieves the next step info
	 * @return string Info about the next step
	 */
	function next_step_info()
	{
		return Translation :: get('Classes_info');
	}
	
	/**
	 * Retrieves the correct message for the correct index, this is used in cooperation with
	 * $failed elements and the method getinfo 
	 * @param int $index place in $failedelements for which the message must be retrieved
	 */
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get('System_Settings'); 
			case 1: return Translation :: get('System_Announcements'); 
			default: return Translation :: get('System_Settings'); 
		}
	}
	
	/**
	 * Builds the next button
	 */
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
	
	/**
	 * Execute the page
	 * Starts migration for system settings and system announcements
	 */
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('systemsettings'))
		{
			echo(Translation :: get('System_Settings') . ' ' .
				 Translation :: get('already_migrated') . '<br />');
			return false;
		}
		
		$logger->write_text('systemsettings');
	
		if($this->command_execute) 
			require(dirname(__FILE__) . '/../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('system_settings.txt');
		$this->logfile->set_start_time();
		
		//Create temporary tables, create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['migrate_settings']) && $exportvalues['migrate_settings'] == 1)
		{	
			//Migrate system settings
			$this->migrate_system_settings();
			
			//Migrate system announcements
			if(isset($exportvalues['migrate_users']) && $exportvalues['migrate_users'] == 1)
			{
				$this->migrate_system_announcements();
			}
			else
			{
				echo(Translation :: get('System_Announcements') . ' ' .
				     Translation :: get('failed') . ' ' .
				     Translation :: get('because') . ' ' . 
				     Translation :: get('Users') . ' ' .
				     Translation :: get('skipped') . '<br />');
				$this->logfile->add_message('System announcements failed because users skipped');
				$this->succes[1] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get('System_Settings') . ' & ' .
			     Translation :: get('System_Announcements')
				 . ' ' . Translation :: get('skipped') . '<br />');
			$this->logfile->add_message('system settings & announcements skipped');
			
			return false;
		}

		
		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		return true;
		
		$logger->close_file();
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
		
		foreach($systemsettings as $i => $systemsetting)
		{
			if($systemsetting->is_valid_current_setting())
			{
				$lcms_admin_setting = $systemsetting->convert_to_new_admin_setting();
				if($lcms_admin_setting)
					$this->logfile->add_message('System setting added ( ID: ' . 
						$lcms_admin_setting->get_id() . ' )');
				$this->succes[0]++;
				unset($lcms_admin_setting);
			}
			else
			{
				/*$message = 'System setting is not valid ( ID: ' . $systemsetting->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;*/
			}
			
			unset($systemsettings[$i]);
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
		$systemannouncements = $systemannouncementsclass->get_all(array('mgdm' => $this->mgdm));
		$id = $this->mgdm->get_id_reference($this->mgdm->get_old_admin_id(), 'user_user');
		
		foreach($systemannouncements as $i => $systemannouncement)
		{
			if($systemannouncement->is_valid_system_announcement())
			{
				$lcms_system_announcement = $systemannouncement->convert_to_new_system_announcement($id);
				$this->logfile->add_message('System announcement added ( ID: ' . 
					$lcms_system_announcement->get_id() . ' )');
				$this->succes[1]++;
				unset($lcms_system_announcement);
			}
			else
			{
				$message = 'System announcment is not valid ( ID: ' . $systemannouncement->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[1][] = $message;
			}
			
			unset($systemannouncements[$i]);
		}
		
		$this->logfile->add_message('System announcements migrated');
	}

}
?>