<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 
/**
 * Class for course groups migration
 * @author Sven Vanpoucke
 */
class GroupsMigrationWizardPage extends MigrationWizardPage
{
	//private $logfile;
	//private $mgdm;
	//private $old_system;
	//private $failed_elements;
	//private $succes;
	//private $command_execute;
	
	/**
	 * Constructor creates a new GroupsMigrationWizardPage
	 * @param string $page_name the page name
	 * @param $parent the parent of the controller
	 * @param bool $command_execute to see if the page is executed by commandline or webinterface
	 */
	function GroupsMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
		$this->command_execute = $command_execute;
	}
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get('Groups_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{		
		for($i=0; $i<1; $i++)
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
		return Translation :: get('Course_meta_info');
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
			//case 0: return Translation :: get('Group categories');
			case 0: return Translation :: get('Groups'); 
			case 1: return Translation :: get('Group_rel_users');
			case 2: return Translation :: get('Group_rel_tutors');  
			default: return Translation :: get('Groups'); 
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
	 * Starts migration for groups, group users , group tutors and group categories
	 */
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('groups'))
		{
			echo(Translation :: get('Groups') . ' ' .
				 Translation :: get('already_migrated') . '<br />');
			return false;
		}
		
		$logger->write_text('groups');
		
		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('groups.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_groups']) && $exportvalues['migrate_groups'] == 1)
		{	
			//Migrate descriptions, settings and tools
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
			
					//$this->migrate_group_categories($course);
					$this->migrate_groups($course);
					//$this->migrate_group_rel_users($course);
					//$this->migrate_group_rel_tutors($course);
					//TODO: group categories, group rel users, group rel tutors;
					unset($courses[$i]);
				}
			}
			else
			{
				echo(Translation :: get('Groups') . ' ' .
				     Translation :: get('failed') . ' ' .
				     Translation :: get('because') . ' ' . 
				     Translation :: get('Users') . ' OR ' .
				     Translation :: get('Courses') . ' ' .
				     Translation :: get('skipped') . '<br />');
				$this->logfile->add_message('Groups failed because users or courses skipped');
				$this->succes[1] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get('Groups')
				 . ' ' . Translation :: get('skipped') . '<br />');
			$this->logfile->add_message('Groups kipped');
			
			return false;
		}

		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		return true;
	}
	
	/**
	 * Migrate the group categories
	 */
	function migrate_group_categories($course)
	{
		$this->logfile->add_message('Starting migration group categories for course ' . $course->get_code());
		
		$groupcatclass = Import :: factory($this->old_system, 'groupcategory');
		$groupcategories = array();
		$groupcategories = $groupcatclass->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name()));
		
		foreach($groupcategories as $j => $groupcategory)
		{
			if($groupcategory->is_valid_group_category($course))
			{
				$lcms_groupcat = $groupcategory->convert_to_new_group_category($course);
				$this->logfile->add_message('SUCCES: Group category added ( ID: ' . $lcms_groupcat->get_id() . ' )');
				$this->succes[0]++;
				unset($lcms_groupcat);
			}
			else
			{
				$message = 'FAILED: Group category is not valid ( ID: ' . $groupcategory->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
		}
		

		$this->logfile->add_message('Group categories migrated for course ' . $course->get_code());
	}
	
	/**
	 * Migrate the groups
	 */
	function migrate_groups($course)
	{
		$this->logfile->add_message('Starting migration groups for course ' . $course->get_code());
		
		$groupclass = Import :: factory($this->old_system, 'group');
		$groups = array();
		$groups = $groupclass->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name()));
		
		foreach($groups as $j => $group)
		{
			if($group->is_valid_group($course))
			{
				$lcms_group = $group->convert_to_new_group($course);
				$this->logfile->add_message('SUCCES: Group added ( ID: ' .  
						$lcms_group->get_id() . ' )');
				$this->succes[0]++;
				unset($lcms_group);
			}
			else
			{
				$message = 'FAILED: Group is not valid ( ID: ' . 
					$group->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
			unset($groups[$j]);
		}
		

		$this->logfile->add_message('Groups migrated for course '. $course->get_code());
	}
	
	/**
	 * migrate course group user relations
	 */
	function migrate_group_rel_users($course)
	{
		$this->logfile->add_message('Starting migration group user relations for course: ' . $course->get_code());
		
		$group_rel_user_class = Import :: factory($this->old_system, 'groupreluser');
		$group_rel_users = array();
		$group_rel_users = $group_rel_user_class->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name()));
		
		foreach($group_rel_users as $group_rel_user)
		{
			if($group_rel_user->is_valid_group_rel_user($course))
			{
				$lcms_group_rel_user = $group_rel_user->convert_to_new_group_rel_user($course);
				$this->logfile->add_message('SUCCES: Group user relation added ( ID: ' . 
						$lcms_group_rel_user->get_id() . ' )');
				$this->succes[2]++;
			}
			else
			{
				$message = 'FAILED: Group user relation is not valid ( ID: ' . $group_rel_user->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[2][] = $message;
			}
		}

		$this->logfile->add_message('Group user relations migrated for course: ' . $course->get_code());
	}
	
	/**
	 * migrate course group tutor relations
	 */
	function migrate_group_rel_tutors($course)
	{
		$this->logfile->add_message('Starting migration group tutor relations for course: ' . $course->get_code());
		
		$group_rel_tutor_class = Import :: factory($this->old_system, 'groupreltutor');
		$group_rel_tutors = array();
		$group_rel_tutors = $group_rel_tutor_class->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name()));
		
		foreach($group_rel_tutors as $group_rel_tutor)
		{
			if($group_rel_tutor->is_valid_group_rel_tutor($course))
			{
				$lcms_group_rel_tutor = $group_rel_tutor->convert_to_new_group_rel_tutor($course);
				$this->logfile->add_message('SUCCES: Group tutor relation added ( ID: ' . 
						$lcms_group_rel_tutor->get_id() . ' )');
				$this->succes[3]++;
			}
			else
			{
				$message = 'FAILED: Group tutor relation is not valid ( ID: ' . $group_rel_tutor->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[3][] = $message;
			}
		}

		$this->logfile->add_message('Group tutor relations migrated for course: ' . $course->get_code());
	}

}
?>