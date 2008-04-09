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
	//private $logfile;
	//private $mgdm;
	//private $old_system;
	//private $failed_elements;
	private $include_deleted_files;
	//private $succes;
	//private $command_execute;
	
	/**
	 * Constructor creates a new CalendarEventsMigrationWizardPage
	 * @param string $page_name the page name
	 * @param $parent the parent of the controller
	 * @param bool $command_execute to see if the page is executed by commandline or webinterface
	 */
	function CalendarEventsMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
		$this->command_execute = $command_execute;
	}
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get('Calendar_events_title');
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
		return Translation :: get('Calendar_events_info');
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
			case 0: return Translation :: get('Calendar_events'); 
			case 1: return Translation :: get('Calendar_resources'); 
			default: return Translation :: get('Calendar_events'); 
		}
	}
	
	/**
	 * Builds a form with a next button
	 */
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
	}
	
	/**
	 * Execute the page
	 * Starts migration for calendar events and resources
	 */
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('calendar_events'))
		{
			echo(Translation :: get('Calendar_events') . ' ' .
				 Translation :: get('already_migrated') . '<br />');
			return false;
		}
		
		$logger->write_text('calendar_events');
		
		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		$this->include_deleted_files = $exportvalues['migrate_deleted_files'];
		
		//Create logfile
		$this->logfile = new Logger('calendar_events.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_calendar_events']) && $exportvalues['migrate_calendar_events'] == 1)
		{	
			//Migrate the calendar events and resources
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
			
					$this->migrate_calendar_events($course);
					//TODO: migrate resources
					//$this->migrate_resources();
					unset($courses[$i]);
				}
			}
			else
			{
				echo(Translation :: get('Calendar_events') . ' and ' .
					 Translation :: get('Calendar_resources') . ' ' .
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
			echo(Translation :: get('Calendar_events')
				 . ' ' . Translation :: get('skipped') . '<br />');
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
		$calendar_events = $class_calendar_event->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name(), 'del_files' => $this->include_deleted_files));
		
		foreach($calendar_events as $j => $calendar_event)
		{
			if($calendar_event->is_valid_calendar_event($course))
			{
				$lcms_calendar_event = $calendar_event->convert_to_new_calendar_event($course);
				$this->logfile->add_message('SUCCES: Calendar event added ( ID ' . $lcms_calendar_event->get_id() . ' )');
				$this->succes[0]++;
				unset($lcms_calendar_event);
			}
			else
			{
				$message = 'FAILED: Calendar event is not valid ( ID ' . $calendar_event->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
			unset($calendar_events[$j]);
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
		$resources = $resourceclass->get_all(array('mgdm' => $this->mgdm, 'course' => $course->get_db_name() ));
		
		foreach($resources as $resource)
		{
			if($resource->is_valid_resource($course))
			{
				$lcms_resource = $resource->convert_to_new_resource($course);
				$this->logfile->add_message('SUCCES: Calendar resource added ( ID: ' .  
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
		

		$this->logfile->add_message('Calendar resources migrated for course '. $course->get_code());
	}

}
?>