<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../import.class.php'; 

/**
 * Class for shared surveys migration
 * @author Sven Vanpoucke
 */
class TrackersMigrationWizardPage extends MigrationWizardPage
{
	private $include_deleted_files;
	
	/**
	 * Constructor creates a new TrackersMigrationWizardPage
	 * @param string $page_name the page name
	 * @param $parent the parent of the controller
	 * @param bool $command_execute to see if the page is executed by commandline or webinterface
	 */
	function TrackersMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent, $command_execute);
		$this->succes = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
	}
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get('Trackers_title');
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
			case 0: return Translation :: get('Tracker_c_os');
			case 1: return Translation :: get('Tracker_c_browsers');
			case 2: return Translation :: get('Tracker_c_countries');
			case 3: return Translation :: get('Tracker_c_providers');
			case 4: return Translation :: get('Tracker_c_referers');
			case 5: return Translation :: get('Tracker_e_access');
			case 6: return Translation :: get('Tracker_e_attempt');
			case 7: return Translation :: get('Tracker_e_courseaccess');
			case 8: return Translation :: get('Tracker_e_default');
			case 9: return Translation :: get('Tracker_e_downloads');
			case 10: return Translation :: get('Tracker_e_exercices');
			case 11: return Translation :: get('Tracker_e_hotpotatoes');
			case 12: return Translation :: get('Tracker_e_hotspot');
			case 13: return Translation :: get('Tracker_e_lastaccess');
			case 14: return Translation :: get('Tracker_e_links');
			case 15: return Translation :: get('Tracker_e_login');
			case 16: return Translation :: get('Tracker_e_online');
			case 17: return Translation :: get('Tracker_e_open');
			case 18: return Translation :: get('Tracker_e_uploads');
			default: return Translation :: get('Tracker_c_os'); 
		}
	}

	/**
	 * Execute the page
	 * Starts migration for all trackers
	 */
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('trackers'))
		{
			echo(Translation :: get('Trackers') . ' ' .
				 Translation :: get('already_migrated') . '<br />');
			return false;
		}
		
		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		$this->include_deleted_files = $exportvalues['migrate_deleted_files'];
		
		//Create logfile
		$this->logfile = new Logger('trackers.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['move_files']) && $exportvalues['move_files'] == 1)
			$this->mgdm->set_move_file(true);
		
		if(isset($exportvalues['migrate_trackers']) && $exportvalues['migrate_trackers'] == 1)
		{	
			if(isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) &&
				$exportvalues['migrate_courses'] == 1 && $exportvalues['migrate_users'] == 1)
			{
				$this->migrate('TrackCOs', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), null,0);
				$this->migrate('TrackCBrowsers', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), null,1);
				$this->migrate('TrackCCountries', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), null,2);
				$this->migrate('TrackCProviders', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), null,3);
				$this->migrate('TrackCReferers', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), null,4);
				//$this->migrate('TrackerEAccess', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,5);
				//$this->migrate('TrackerEAttempt', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,6);
				//$this->migrate('TrackerECourseaccess', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,7);
				//$this->migrate('TrackerEDefault', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,8);
				//$this->migrate('TrackerEDownloads', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,9);
				//$this->migrate('TrackerEExercices', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,10);
				//$this->migrate('TrackerEHotpotatoes', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,11);
				//$this->migrate('TrackerEHotspot', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,12);
				//$this->migrate('TrackerELastaccess', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,13);
				//$this->migrate('TrackerELinks', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,14);
				$this->migrate('TrackELogin', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), null,15);
				//$this->migrate('TrackerEOnline', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,16);
				//$this->migrate('TrackerEOpen', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,17);
				//$this->migrate('TrackerEUploads', array('mgdm' => $this->mgdm, 'del_files' => $this->include_deleted_files), array(), $course,18);
			}
			else
			{
				echo(Translation :: get('Trackers') .
				     Translation :: get('failed') . ' ' .
				     Translation :: get('because') . ' ' . 
				     Translation :: get('Users') . ' ' .
				     Translation :: get('skipped') . '<br />');
				$this->logfile->add_message('Trackers failed because users or courses skipped');
				$this->succes = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			}
			
		}
		else
		{
			echo(Translation :: get('Trackers')
				 . ' ' . Translation :: get('skipped') . '<br />');
			$this->logfile->add_message('Trackers skipped');
			
			return false;
		}

		//Close the logfile
		$this->passedtime = $this->logfile->write_passed_time();
		$this->logfile->close_file();
		
		$logger->write_text('trackers');
		
		return true;
	}

}
?>