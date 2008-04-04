<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 

/**
 * Class for course scorms migration
 * @author Sven Vanpoucke
 */
class ScormsMigrationWizardPage extends MigrationWizardPage
{
	private $include_deleted_files;
	
	function ScormsMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent, $command_execute);
		$this->succes = array(0);
	}
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Scorms_title');
	}
	
	function next_step_info()
	{
		return Translation :: get_lang('Scorms_info');
	}
	
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('Scorm_documents');
			default: return Translation :: get_lang('Scorm_documents'); 
		}
	}

	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('scorms'))
		{
			echo(Translation :: get_lang('Scorms') . ' ' .
				 Translation :: get_lang('already_migrated') . '<br />');
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
		$this->logfile = new Logger('scorms.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_scorms']) && $exportvalues['migrate_scorms'] == 1)
		{	
			//Migrate the dropbox
			if(isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) &&
				$exportvalues['migrate_courses'] == 1 && $exportvalues['migrate_users'] == 1)
			{
				$courseclass = Import :: factory($this->old_system, 'course');
				$courses = array();
				$courses = $courseclass->get_all(array('mgdm' => $this->mgdm));
				
				foreach($courses as $i => $course)
				{
					$old_rel_path = 'courses/' . $course->get_directory() . '/scorm/';
					$old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);
					$full_path = $this->mgdm->append_full_path(false,$old_rel_path);					

					if ($this->mgdm->get_failed_element('dokeos_main.course', $course->get_code()) || !is_dir($full_path))
					{
						continue;
					}	
					
					$this->migrate('Scormdocument', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,0);
					
					unset($courses[$i]);
				}
			}
			else
			{
				echo(Translation :: get_lang('Scorms') .
				     Translation :: get_lang('failed') . ' ' .
				     Translation :: get_lang('because') . ' ' . 
				     Translation :: get_lang('Users') . ' ' .
				     Translation :: get_lang('skipped') . '<br />');
				$this->logfile->add_message('Scorms failed because users or courses skipped');
				$this->succes = array(0);
			}
			
		}
		else
		{
			echo(Translation :: get_lang('Scorms')
				 . ' ' . Translation :: get_lang('skipped') . '<br />');
			$this->logfile->add_message('Scorms skipped');
			
			return false;
		}

		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		$logger->write_text('scorms');
		
		return true;
	}

}
?>
