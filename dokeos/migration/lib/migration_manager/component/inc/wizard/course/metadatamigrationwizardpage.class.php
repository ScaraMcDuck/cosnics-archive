<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 
/**
 * Class for course meta data migration
 * @author Sven Vanpoucke
 */
class MetaDataMigrationWizardPage extends MigrationWizardPage
{
	//private $logfile;
	//private $mgdm;
	//private $old_system;
	//private $failed_elements;
	//private $succes;
	//private $command_execute;
	
	/**
	 * Constructor creates a new MetaDataMigrationWizardPage
	 * @param string $page_name the page name
	 * @param $parent the parent of the controller
	 * @param bool $command_execute to see if the page is executed by commandline or webinterface
	 */
	function MetaDataMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
		$this->command_execute = $command_execute;
		$this->succes = array(0,0,0,0);
	}
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get('Course_meta_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{		
		for($i=0; $i<4; $i++)
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
			case 0: return Translation :: get('Course_meta_Descriptions'); 
			case 1: return Translation :: get('Course_meta_Tools');
			case 2: return Translation :: get('Course_meta_Settings');  
			case 3: return Translation :: get('Course_tool_intros');  
			default: return Translation :: get('Course_meta_Descriptions'); 
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
	 * Starts migration for descriptions, settings, tools, tool intros
	 */
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('metadata'))
		{
			echo(Translation :: get('Course_metadata') . ' ' .
				 Translation :: get('already_migrated') . '<br />');
			return false;
		}
		
		$logger->write_text('metadata');
		
		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('metadata.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_metadata']) && $exportvalues['migrate_metadata'] == 1)
		{	
			//Migrate descriptions, settings and tools
			if(isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) &&
					 $exportvalues['migrate_courses'] == 1 &&  $exportvalues['migrate_users'] == 1)
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
			
					$this->migrate_descriptions($course);
					//$this->migrate_settings($course);
					//$this->migrate_tools($course);
					$this->migrate('ToolIntro', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,3);
					unset($courses[$i]);
				}
			}
			else
			{
				echo(Translation :: get('Course_metadata') . ' ' .
				     Translation :: get('failed') . ' ' .
				     Translation :: get('because') . ' ' . 
				     Translation :: get('Users') . ' OR ' .
				     Translation :: get('Courses') . ' ' .
				     Translation :: get('skipped') . '<br />');
				$this->logfile->add_message('Course metadata failed because users or courses skipped');
				$this->succes = array(0,0,0,0);;
			}
			
		}
		else
		{
			echo(Translation :: get('Course_metadata')
				 . ' ' . Translation :: get('skipped') . '<br />');
			$this->logfile->add_message('Course metadata kipped');
			
			return false;
		}

		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		return true;
	}
	
	/**
	 * Migrate the descriptions
	 */
	function migrate_descriptions($course)
	{
		$this->logfile->add_message('Starting migration descriptions for course ' . $course->get_code());
		
		$descriptions_class = Import :: factory($this->old_system, 'coursedescription');
		$descriptions = array();
		$descriptions = $descriptions_class->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name()));
		
		foreach($descriptions as $j => $description)
		{
			if($description->is_valid_course_description($course))
			{
				$lcms_description = $description->convert_to_new_course_description($course);
				$this->logfile->add_message('SUCCES: Description added ( ID: ' . $lcms_description->get_id() . ' )');
				$this->succes[0]++;
				unset($lcms_description);
			}
			else
			{
				$message = 'FAILED: Description is not valid ( ID: ' . $description->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
			unset($descriptions[$j]);
		}
		

		$this->logfile->add_message('Descriptions migrated for course ' . $course->get_code());
	}
	
	/**
	 * Migrate the settings
	 */
	function migrate_settings($course)
	{
		$this->logfile->add_message('Starting migration settings for course' . $course->get_code());
		
		$settingsclass = Import :: factory($this->old_system, 'coursesetting');
		$settings = array();
		$settings = $settingsclass->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name()));
		
		foreach($settings as $setting)
		{
			if($setting->is_valid_setting($course))
			{
				$lcms_setting = $setting->convert_to_new_setting($course);
				$this->logfile->add_message('SUCCES: Link added ( ID: ' .  
						$lcms_setting->get_id() . ' )');
				$this->succes[1]++;
			}
			else
			{
				$message = 'FAILED: Link is not valid ( ID: ' . 
					$setting->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[1][] = $message;
			}
		}
		

		$this->logfile->add_message('Settings migrated for course '. $course->get_code());
	}
	
	/**
	 * migrate course tools
	 */
	function migrate_course_tools($course)
	{
		$this->logfile->add_message('Starting migration course tools for course: ' . $course->get_code());
		
		$tool_class = Import :: factory($this->old_system, 'tool');
		$tools = array();
		$tools = $tool_class->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name()));
		
		foreach($tools as $tool)
		{
			if($tool->is_valid_tool())
			{
				$lcms_tool = $tool->convert_to_new_tool();
				$this->logfile->add_message('SUCCES: Course tool added ( ID: ' . 
						$lcms_tool->get_id() . ' )');
				$this->succes[2]++;
			}
			else
			{
				$message = 'FAILED: Course tool is not valid ( ID: ' . $tool->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[2][] = $message;
			}
		}

		$this->logfile->add_message('Course tools migrated for course: ' . $course->get_code());
	}

}
?>