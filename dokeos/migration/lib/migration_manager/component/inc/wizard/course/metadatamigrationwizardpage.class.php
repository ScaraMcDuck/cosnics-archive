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
	private $logfile;
	private $mgdm;
	private $old_system;
	private $failed_elements;
	private $succes;
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Course_meta_title');
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
	
	function next_step_info()
	{
		return Translation :: get_lang('Course_meta_info');
	}
	
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('Course_meta_Descriptions'); 
			case 1: return Translation :: get_lang('Course_meta_Tools');
			case 1: return Translation :: get_lang('Course_meta_Settings');  
			default: return Translation :: get_lang('Course_meta_Descriptions'); 
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
		
		if($logger->is_text_in_file('metadata'))
		{
			echo(Translation :: get_lang('Course_metadata') . ' ' .
				 Translation :: get_lang('already_migrated') . '<br />');
			return false;
		}
		
		$logger->write_text('metadata');
		
		$exportvalues = $this->controller->exportValues();
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('metadata.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['migrate_metadata']) && $exportvalues['migrate_metadata'] == 1)
		{	
			//Migrate descriptions, settings and tools
			if(isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']))
			{
				$courseclass = Import :: factory($this->old_system, 'course');
				$courses = array();
				$courses = $courseclass->get_all_courses($this->mgdm);
				
				foreach($courses as $course)
				{
					if ($this->mgdm->get_failed_element('dokeos_main.course', $course->get_code()))
					{
						continue;
					}	
			
					$this->migrate_descriptions($course);
					//$this->migrate_settings($course);
					//$this->migrate_tools($course);
				}
			}
			else
			{
				echo(Translation :: get_lang('Course_metadata') . ' ' .
				     Translation :: get_lang('failed') . ' ' .
				     Translation :: get_lang('because') . ' ' . 
				     Translation :: get_lang('Users') . ' OR ' .
				     Translation :: get_lang('Courses') . ' ' .
				     Translation :: get_lang('skipped') . '<br />');
				$this->logfile->add_message('Course metadata failed because users or courses skipped');
				$this->succes[1] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get_lang('Course_metadata')
				 . ' ' . Translation :: get_lang('skipped') . '<br />');
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
		$descriptions = $descriptions_class->get_all_course_descriptions($course->get_db_name(), $this->mgdm);
		
		foreach($descriptions as $description)
		{
			if($description->is_valid_course_description($course))
			{
				$lcms_description = $description->convert_to_new_course_description($course);
				$this->logfile->add_message('SUCCES: Description added ( ID: ' . $lcms_description->get_id() . ' )');
				$this->succes[0]++;
			}
			else
			{
				$message = 'FAILED: Description is not valid ( ID: ' . $description->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
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
		$settings = $settingsclass->get_all_settings($course->get_db_name(),$this->mgdm);
		
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
		$tools = $tool_class->get_all_tools($this->mgdm, $course->get_db_name());
		
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
				$this->failed_elements[5][] = $message;
			}
		}

		$this->logfile->add_message('Course tools migrated for course: ' . $course->get_code());
	}

}
?>