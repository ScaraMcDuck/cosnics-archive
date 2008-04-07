<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 

/**
 * Class for gradebooks migration
 * @author Sven Vanpoucke
 */
class GradebooksMigrationWizardPage extends MigrationWizardPage
{
	private $include_deleted_files;
	
	function GradebooksMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent, $command_execute);
		$this->succes = array(0,0,0,0,0);
	}
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Gradebooks_title');
	}
	
	function next_step_info()
	{
		return Translation :: get_lang('Gradebooks_info');
	}
	
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('Gradebook_categories');
			case 1: return Translation :: get_lang('Gradebook_evaluations');
			case 2: return Translation :: get_lang('Gradebook_links');
			case 3: return Translation :: get_lang('Gradebook_result');
			case 4: return Translation :: get_lang('Gradebook_score_displays');
			default: return Translation :: get_lang('Gradebook'); 
		}
	}

	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('gradebooks'))
		{
			echo(Translation :: get_lang('Gradebooks') . ' ' .
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
		$this->logfile = new Logger('gradebooks.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_gradebooks']) && $exportvalues['migrate_gradebooks'] == 1)
		{	
			//Migrate the dropbox
			if(isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) &&
				$exportvalues['migrate_courses'] == 1 && $exportvalues['migrate_users'] == 1)
			{
				//$this->migrate('GradebookCategory', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,0);
				//$this->migrate('GradebookEvaluation', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,1);
				//$this->migrate('GradebookLink', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,2);
				//$this->migrate('GradebookResult', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,3);
				//$this->migrate('GradebookScoreDisplay', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,4);
			}
			else
			{
				echo(Translation :: get_lang('Gradebooks') .
				     Translation :: get_lang('failed') . ' ' .
				     Translation :: get_lang('because') . ' ' . 
				     Translation :: get_lang('Users') . ' ' .
				     Translation :: get_lang('skipped') . '<br />');
				$this->logfile->add_message('Gradebooks failed because users or courses skipped');
				$this->succes = array(0,0,0,0,0);
			}
			
		}
		else
		{
			echo(Translation :: get_lang('Gradebooks')
				 . ' ' . Translation :: get_lang('skipped') . '<br />');
			$this->logfile->add_message('Gradebooks skipped');
			
			return false;
		}

		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		$logger->write_text('gradebooks');
		
		return true;
	}

}
?>