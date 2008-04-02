<?php

/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 
require_once dirname(__FILE__) . '/../../../../../../../repository/lib/learning_object/announcement/announcement.class.php'; 

/**
 * Class for user migration execution
 * 
 */
class AnnouncementsMigrationWizardPage extends MigrationWizardPage
{
	//private $logfile;
	//private $mgdm;
	//private $old_system;
	private $include_deleted_files;
	//private $failed_announcements = array();
	//private $succes;
	//private $command_execute;
	
	function AnnouncementsMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
		$this->command_execute = $command_execute;
	}
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Announcements_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{		
		for($i=0; $i<1; $i++)
		{
			$message = $message . '<br />' . $this->succes[$i] . ' ' . $this->get_message($i) . ' ' .
				Translation :: get_lang('migrated');
			
			if(count($this->failed_elements[$i]) > 0)
				$message = $message . '<br / >' . count($this->failed_elements[$i]) . ' ' .
					 $this->get_message($i) . ' ' . Translation :: get_lang('failed');
			
			foreach($this->failed_elements[$i] as $felement)
			{
				$message = $message . '<br />' . $felement ;
			}
			
			$message = $message . '<br />';
		}
		
		$message = $message . '<br />' . Translation :: get_lang('Dont_forget');
		
		return $message;
	}
	
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('Announcements'); 
			default: return Translation :: get_lang('Announcements'); 
		}
	}
	
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
	}
	
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('announcements'))
		{
			echo(Translation :: get_lang('Announcements') . ' ' .
				 Translation :: get_lang('already_migrated') . '<br />');
			return false;
		}
		
		$logger->write_text('announcements');
		
		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		$this->include_deleted_files = $exportvalues['migrate_deleted_files'];
		
		//Create logfile
		$this->logfile = new Logger('announcements.txt');
		$this->logfile->set_start_time();
		
		//Create temporary tables, create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_announcements']) && $exportvalues['migrate_announcements'] == 1)
		{	
			//Migrate the personal agendas
			if(isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) &&
					$exportvalues['migrate_courses'] == 1 && $exportvalues['migrate_users'] == 1)
			{
				$this->migrate_announcements();
			}
			else
			{
				echo(Translation :: get_lang('Announcements') . ' ' .
				     Translation :: get_lang('failed') . ' ' .
				     Translation :: get_lang('because') . ' ' . 
				     Translation :: get_lang('Courses') . ' ' .
				     Translation :: get_lang('skipped') . '<br />');
				$this->logfile->add_message('Announcements failed because courses skipped');
				$this->succes[0] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get_lang('Announcements')
				 . ' ' . Translation :: get_lang('skipped') . '<br />');
			$this->logfile->add_message('Annoucements skipped');
		}
	
		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		return true;
	}
	
	function migrate_announcements()
	{
		$this->logfile->add_message('Starting migration courses announcements');
		
		$announcementclass = Import :: factory($this->old_system, 'announcement');
		$courseclass = Import :: factory($this->old_system, 'course');
		
		$announcements = array();
		$courses = array();
		
		$courses = $courseclass->get_all(array('mgdm' => $this->mgdm));
		
		foreach($courses as $i => $course)
		{
			if ($this->mgdm->get_failed_element('dokeos_main.course', $course->get_code()))
			{
				continue;
			}
			
			$announcements = $announcementclass->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name(), 'del_files' => $this->include_deleted_files));
			
			foreach($announcements as $j => $announcement)
			{
				if($announcement->is_valid_announcement($course))
				{
					$lcms_announcement = $announcement->convert_to_new_announcement($course);
					$this->logfile->add_message('SUCCES: Announcement added ( ID:' . $lcms_announcement->get_id() . ' )');
					$this->succes[0]++;
					unset($lcms_announcement);
				}
				else
				{
					$message = 'FAILED: Announcement is not valid ( ID: ' . $announcement->get_id() . ')';
					$this->logfile->add_message($message);
					$this->failed_announcements[] = $message;
				}
				
				$this->logfile->add_message('Announcements ' . $course->get_code() . ' migrated');
				unset($announcements[$j]);
			}
			unset($courses[$i]);
		}

		$this->logfile->add_message('Announcements courses migrated');
	}

}
?>