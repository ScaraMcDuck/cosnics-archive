<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 
/**
 * Class for course calendar events migration
 * @author Sven Vanpoucke
 */
class CalendarEventsMigrationWizardPage extends MigrationWizardPage
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
		return Translation :: get_lang('Calendar_events_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{		
		for($i=0; $i<2; $i++)
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
		return Translation :: get_lang('Calendar_events_info');
	}
	
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('Calendar_events'); 
			case 1: return Translation :: get_lang('Calendar_resources'); 
			default: return Translation :: get_lang('Calendar_events'); 
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
		
		if($logger->is_text_in_file('calendar_events'))
		{
			echo(Translation :: get_lang('Calendar_events') . ' ' .
				 Translation :: get_lang('already_migrated') . '<br />');
			return false;
		}
		
		$logger->write_text('calendar_events');
		
		$exportvalues = $this->controller->exportValues();
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		
		//Create logfile
		$this->logfile = new Logger('calendar_events.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['migrate_calendar_events']) && $exportvalues['migrate_calendar_events'] == 1)
		{	
			//Migrate the calendar events and resources
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
			
					$this->migrate_calendar_events($course);
					//TODO: migrate resources
					//$this->migrate_resources();
				}
			}
			else
			{
				echo(Translation :: get_lang('Calendar_events') . ' and ' .
					 Translation :: get_lang('Calendar_resources') . ' ' .
				     Translation :: get_lang('failed') . ' ' .
				     Translation :: get_lang('because') . ' ' . 
				     Translation :: get_lang('Users') . ' ' .
				     Translation :: get_lang('skipped') . '<br />');
				$this->logfile->add_message('Calendar events failed because users skipped');
				$this->succes[1] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get_lang('Calendar_events')
				 . ' ' . Translation :: get_lang('skipped') . '<br />');
			$this->logfile->add_message('Calendar events skipped');
			
			return false;
		}

		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		return true;
	}
	
	/**
	 * Migrate the calendar events
	 */
	function migrate_calendar_events($course)
	{
		$this->logfile->add_message('Starting migration calendarevents for course ' . $course->get_code());
		
		$class_calendar_event = Import :: factory($this->old_system, 'calendarevent');
		$calendar_events = array();
		$calendar_events = $class_calendar_event->get_all_calendar_events($course, $this->mgdm);
		
		foreach($calendar_events as $calendar_event)
		{
			if($calendar_event->is_valid_calendar_event($course))
			{
				$lcms_calendar_event = $calendar_event->convert_to_new_calendar_event($course);
				$this->logfile->add_message('SUCCES: Calendar event added ( ' . $lcms_calendar_event->get_id() . ' )');
				$this->succes[0]++;
			}
			else
			{
				$message = 'FAILED: Calendar event is not valid ( ID ' . $calendar_event->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
		}
		

		$this->logfile->add_message('Calendar events migrated for course ' . $course->get_code());
	}
	
	/**
	 * Migrate the calendar resources
	 */
	function migrate_calendar_resources($course)
	{
		$this->logfile->add_message('Starting migration calendar resources for course' . $course->get_code());
		
		$resourceclass = Import :: factory($this->old_system, 'resource');
		$resources = array();
		$resources = $resourceclass->get_all_resources($course,$this->mgdm);
		
		foreach($resources as $resource)
		{
			if($resource->is_valid_resource($course))
			{
				$lcms_resource = $resource->convert_to_new_resource($course);
				$this->logfile->add_message('SUCCES: Calendar resource user added ( ID: ' .  
						$lcms_resource->get_id() . ' )');
				$this->succes[1]++;
			}
			else
			{
				$message = 'FAILED: Calendar resource is not valid ( ID: ' . 
					$resource->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[1][] = $message;
			}
		}
		

		$this->logfile->add_message('Calendar resources migrated '. $course->get_code());
	}

}
?>