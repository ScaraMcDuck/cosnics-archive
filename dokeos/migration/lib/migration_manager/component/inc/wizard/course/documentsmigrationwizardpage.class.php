<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 
/**
 * Class for course documents migration
 * @author Van Wayenbergh David
 */
class DocumentsMigrationWizardPage extends MigrationWizardPage
{
	private $include_deleted_files;
	
	/**
	 * Constructor creates a new DocumentsMigrationWizardPage
	 * @param string $page_name the page name
	 * @param $parent the parent of the controller
	 * @param bool $command_execute to see if the page is executed by commandline or webinterface
	 */
	function DocumentsMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
		$this->command_execute = $command_execute;
		$this->succes = array(0);
	}
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get('Documents_title');
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
			case 0: return Translation :: get('Documents'); 
			default: return Translation :: get('Documents'); 
		}
	}
	
	/**
	 * Execute the page
	 * Starts migration for documents
	 */
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('documents'))
		{
			echo(Translation :: get('Documents') . ' ' .
				 Translation :: get('already_migrated') . '<br />');
			return false;
		}
		
		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		$this->include_deleted_files = $exportvalues['migrate_deleted_files'];
		
		//Create logfile
		$this->logfile = new Logger('documents.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->old_mgdm = OldMigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->old_mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_documents']) && $exportvalues['migrate_documents'] == 1)
		{	
			//Migrate the calendar events and resources
			if(isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) &&
					 $exportvalues['migrate_courses'] == 1 && $exportvalues['migrate_users'] == 1)
			{
				$courseclass = Import :: factory($this->old_system, 'course');
				$courses = array();
				$courses = $courseclass->get_all(array('old_mgdm' => $this->old_mgdm));
				$mgdm = MigrationDataManager :: get_instance();
				foreach($courses as $i => $course)
				{
					$old_rel_path = 'courses/' . $course->get_directory() . '/document/';
					$old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);
					$full_path = $this->old_mgdm->append_full_path(false,$old_rel_path);
					
					if ($mgdm->get_failed_element('dokeos_main.course', $course->get_code()) || !is_dir($full_path))
					{
						continue;
					}	
					
					$condition = new EqualityCondition('filetype', 'file');
					
					//$this->migrate_documents($course);
					$this->migrate('Document', array('old_mgdm' => $this->old_mgdm, 'del_files' => $this->include_deleted_files, 'condition' => $condition), array('old_mgdm' => $this->old_mgdm), $course,0);
					unset($courses[$i]);
					flush();
				}
			}
			else
			{
				echo(Translation :: get('Documents') . ' ' .
				     Translation :: get('failed') . ' ' .
				     Translation :: get('because') . ' ' . 
				     Translation :: get('Users') . ' ' .
				     Translation :: get('skipped') . '<br />');
				$this->logfile->add_message('Calendar events failed because users skipped');
				$this->succes[1] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get('Documents')
				 . ' ' . Translation :: get('skipped') . '<br />');
			$this->logfile->add_message('Documents skipped');
			
			return false;
		}

		//Close the logfile
		$this->passedtime = $this->logfile->write_passed_time();
		$this->logfile->close_file();
		$logger->write_text('documents');
		$logger->close_file();
		return true;
	}
}
?>
