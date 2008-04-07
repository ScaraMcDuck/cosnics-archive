<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 

/**
 * Class for blogs migration
 * @author Sven Vanpoucke
 */
class BlogsMigrationWizardPage extends MigrationWizardPage
{
	private $include_deleted_files;
	
	function BlogsMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent, $command_execute);
		$this->succes = array(0,0,0,0,0,0,0);
	}
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Blogs_title');
	}
	
	function next_step_info()
	{
		return Translation :: get_lang('Blogs_info');
	}
	
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('Blogs');
			case 1: return Translation :: get_lang('Blog_comments');
			case 2: return Translation :: get_lang('Blog_posts');
			case 3: return Translation :: get_lang('Blog_ratings');
			case 4: return Translation :: get_lang('Blog_rel_users');
			case 5: return Translation :: get_lang('Blog_tasks');
			case 6: return Translation :: get_lang('Blog_task_rel_users');
			default: return Translation :: get_lang('Blogs'); 
		}
	}

	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('blogs'))
		{
			echo(Translation :: get_lang('Blogs') . ' ' .
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
		$this->logfile = new Logger('blogs.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_blogs']) && $exportvalues['migrate_blogs'] == 1)
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
					if ($this->mgdm->get_failed_element('dokeos_main.course', $course->get_code()))
					{
						continue;
					}	
					
					//$this->migrate('Blog', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,0);
					//$this->migrate('BlogComment', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,1);
					//$this->migrate('BlogPost', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,2);
					//$this->migrate('BlogRating', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,3);
					//$this->migrate('BlogRelUser', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,4);
					//$this->migrate('BlogTask', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,5);
					//$this->migrate('BlogTaskRelUser', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,6);
					unset($courses[$i]);
				}
			}
			else
			{
				echo(Translation :: get_lang('Blogs') .
				     Translation :: get_lang('failed') . ' ' .
				     Translation :: get_lang('because') . ' ' . 
				     Translation :: get_lang('Users') . ' ' .
				     Translation :: get_lang('skipped') . '<br />');
				$this->logfile->add_message('Blogs failed because users or courses skipped');
				$this->succes = array(0,0,0,0,0,0,0);
			}
			
		}
		else
		{
			echo(Translation :: get_lang('Blogs')
				 . ' ' . Translation :: get_lang('skipped') . '<br />');
			$this->logfile->add_message('Blogs skipped');
			
			return false;
		}

		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		$logger->write_text('blogs');
		
		return true;
	}

}
?>