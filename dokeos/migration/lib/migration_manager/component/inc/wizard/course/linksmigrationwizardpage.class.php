<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 
/**
 * Class for course links migration
 * @author Sven Vanpoucke
 */
class LinksMigrationWizardPage extends MigrationWizardPage
{
	//private $logfile;
	//private $mgdm;
	//private $old_system;
	//private $failed_elements;
	private $include_deleted_files;
	//private $succes;
	//private $command_execute;
	
	function LinksMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
		$this->command_execute = $command_execute;
	}
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get('Links_title');
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
	
	function next_step_info()
	{
		return Translation :: get('Links_info');
	}
	
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get('Link_categories'); 
			case 1: return Translation :: get('Links'); 
			default: return Translation :: get('Link_categories'); 
		}
	}
	
	
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
	}
	
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('links'))
		{
			echo(Translation :: get('Links') . ' ' .
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
		$this->logfile = new Logger('links.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_links']) && $exportvalues['migrate_links'] == 1)
		{	
			//Migrate link categories and the links 
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
			
					$this->migrate_link_categories($course);
					$this->migrate_links($course);
					unset($courses[$i]);
				}
			}
			else
			{
				echo(Translation :: get('Link_categories') . ' and ' .
					 Translation :: get('Links') . ' ' .
				     Translation :: get('failed') . ' ' .
				     Translation :: get('because') . ' ' . 
				     Translation :: get('Users') . ' ' .
				     Translation :: get('skipped') . '<br />');
				$this->logfile->add_message('Links failed because users skipped');
				$this->succes[1] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get('Links')
				 . ' ' . Translation :: get('skipped') . '<br />');
			$this->logfile->add_message('Links kipped');
			
			return false;
		}

		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		$logger->write_text('links');

		return true;
	}
	
	/**
	 * Migrate the link categories
	 */
	function migrate_link_categories($course)
	{
		$this->logfile->add_message('Starting migration link categories for course ' . $course->get_code());
		
		$class_link_categories = Import :: factory($this->old_system, 'linkcategory');
		$link_categories = array();
		$link_categories = $class_link_categories->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name()));
		
		foreach($link_categories as $j => $link_category)
		{
			if($link_category->is_valid_link_category($course))
			{
				$lcms_link_category = $link_category->convert_to_new_link_category($course);
				$this->logfile->add_message('SUCCES: Link category added ( ID: ' . $lcms_link_category->get_id() . ' )');
				$this->succes[0]++;
				unset($lcms_link_category);
			}
			else
			{
				$message = 'FAILED: Link category is not valid ( ID: ' . $link_category->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
			unset($link_categories[$j]);
		}
		

		$this->logfile->add_message('Link categories migrated for course ' . $course->get_code());
	}
	
	/**
	 * Migrate the links
	 */
	function migrate_links($course)
	{
		$this->logfile->add_message('Starting migration links for course' . $course->get_code());
		
		$linkclass = Import :: factory($this->old_system, 'link');
		$links = array();
		$links = $linkclass->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name(), 'del_files' => $this->include_deleted_files));
		
		foreach($links as $j => $link)
		{
			if($link->is_valid_link($course))
			{				
				$lcms_link = $link->convert_to_new_link($course);
				$this->logfile->add_message('SUCCES: Link added ( ID: ' .  
						$lcms_link->get_id() . ' )');
				$this->succes[1]++;
				unset($lcms_link);
			}
			else
			{
				$message = 'FAILED: Link is not valid ( ID: ' . 
					$link->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[1][] = $message;
			}
			unset($links[$j]);
		}
		

		$this->logfile->add_message('Links migrated for course '. $course->get_code());
	}

}
?>
