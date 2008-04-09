<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 

/**
 * Class for sessions migration
 * @author Sven Vanpoucke
 */
class SessionsMigrationWizardPage extends MigrationWizardPage
{
	private $include_deleted_files;
	
	/**
	 * Constructor creates a new SessionsMigrationWizardPage
	 * @param string $page_name the page name
	 * @param $parent the parent of the controller
	 * @param bool $command_execute to see if the page is executed by commandline or webinterface
	 */
	function SessionsMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent, $command_execute);
		$this->succes = array(0,0,0,0,0);
	}
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Sessions_title');
	}
	
	/**
	 * Retrieves the next step info
	 * @return string Info about the next step
	 */
	function next_step_info()
	{
		return Translation :: get_lang('Sessions_info');
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
			case 0: return Translation :: get_lang('Sessions');
			case 1: return Translation :: get_lang('Php_sessions');
			case 2: return Translation :: get_lang('Session_rel_courses');
			case 3: return Translation :: get_lang('Session_rel_course_rel_users');
			case 4: return Translation :: get_lang('Session_rel_users');
			default: return Translation :: get_lang('Sessions'); 
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
	}
	
	/**
	 * Execute the page
	 * Starts migration for sessions, php sessions, session rel course, session rel course rel user and session rel user
	 */
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('sessions'))
		{
			echo(Translation :: get_lang('Sessions') . ' ' .
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
		$this->logfile = new Logger('sessions.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_sessions']) && $exportvalues['migrate_sessions'] == 1)
		{	
			//Migrate the dropbox
			if(isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) &&
				$exportvalues['migrate_courses'] == 1 && $exportvalues['migrate_users'] == 1)
			{
				//$this->migrate('PhpSession', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,0);
				//$this->migrate('Session', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,1);
				//$this->migrate('SessionRelCourse', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,2);
				//$this->migrate('SessionRelCourseRelUser', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,3);
				//$this->migrate('SessionRelUser', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,4);
			}
			else
			{
				echo(Translation :: get_lang('Sessions') .
				     Translation :: get_lang('failed') . ' ' .
				     Translation :: get_lang('because') . ' ' . 
				     Translation :: get_lang('Users') . ' ' .
				     Translation :: get_lang('skipped') . '<br />');
				$this->logfile->add_message('Sessions failed because users or courses skipped');
				$this->succes = array(0,0,0,0,0);
			}
			
		}
		else
		{
			echo(Translation :: get_lang('Sessions')
				 . ' ' . Translation :: get_lang('skipped') . '<br />');
			$this->logfile->add_message('Sessions skipped');
			
			return false;
		}

		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		$logger->write_text('sessions');
		
		return true;
	}

}
?>