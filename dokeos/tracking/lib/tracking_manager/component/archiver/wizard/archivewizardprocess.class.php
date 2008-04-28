<?php
/**
 * @package tracking.lib.tracking_manager
 * @subpackage component.archiver.wizard
 */

require_once Path :: get_library_path() . 'installer.class.php';

/**
 * This class implements the action to take after the user has completed a
 * archive trackers wizard
 * @author Sven Vanpoucke
 */
class ArchiveWizardProcess extends HTML_QuickForm_Action
{
	/**
	 * The component in which the wizard runs
	 */
	private $parent;
	private $tdm;
	/**
	 * Constructor
	 * @param TrackingManagerArchiveComponent $parent The component in which the wizard runs
	 */
	public function ArchiveWizardProcess($parent)
	{
		$this->parent = $parent;
		$this->tdm = TrackingDataManager :: get_instance();
	}
	
	/**
	 * Executes this page
	 * @param ArchiveWizardPage $page the page that has to be executed
	 * @param string $actionName the action
	 */
	function perform($page, $actionName)
	{
		$exports = $page->controller->exportValues();
		
		// Display the page header
		$trail = new BreadCrumbTrail();
		$trail->add(new Breadcrumb($this->parent->get_url(array(TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_ARCHIVE)), Translation :: get('Archiver')));
	
		$this->parent->display_header($trail);
		
		$sd = $exports['start_date'];
		$startdate = $sd['Y'] . '-' . $sd['M'] .'-' . $sd['d']; 
		$startdate = RepositoryUtilities :: time_from_datepicker_without_timepicker($startdate);

		$ed = $exports['end_date'];
		$enddate = mktime(23, 59, 59, $ed['M'], $ed['d'], $ed['Y']);
		
		$period = $exports['period'];
		
		foreach($exports as $key => $export)
		{	
			if(substr($key, strlen($key) - strlen('event'), strlen($key)) == 'event')
			{
				$eventname = substr($key, 0, strlen($key) - strlen('event'));
		
				$event = $this->parent->retrieve_event_by_name($eventname);
				
				echo(Translation :: get('Archiving_event') . ': ' . $eventname . '<br />');
				
				foreach($exports as $key2 => $export2)
				{
					if((strpos($key2, $eventname) !== false) && ($key2 != $key))
					{	
						$id = substr($key2, strlen($eventname . 'event'));
						$trackerregistration = $this->parent->retrieve_tracker_registration($id);

						$classname = $trackerregistration->get_class();
						echo(' &nbsp; &nbsp; ' . Translation :: get('Archiving_tracker') . ': ' . $classname . '...');
						
						$filename = RepositoryUtilities :: camelcase_to_underscores($classname);
			
						$fullpath = Path :: get(SYS_PATH) . $trackerregistration->get_path() . 
							strtolower($filename) . '.class.php';
						require_once($fullpath);
						
						$tracker = new $classname;
						
						$path = Path :: get(SYS_PATH) . $trackerregistration->get_path() . 'tracker_tables/' . $tracker->get_table() . '.xml';
						
						$storage_units = array();
						
						if($tracker->is_summary_tracker())
						{ 
							$storage_units[] = $tracker->get_table() . '_' . $startdate;
							if($this->create_storage_unit($path, '_' . $startdate))
								$this->create_archive_controller_item($tracker->get_table(), $startdate, $period, $enddate);
						}
						else
						{
							$difference = gregoriantojd($ed['M'],$ed['d'], $ed['Y']) - 
								gregoriantojd($sd['M'],$sd['d'], $sd['Y']);
							
							$amount_of_tables = ceil($difference / $period);
							
							for($i = 0; $i < $amount_of_tables; $i++)
							{
								$added_days = $i * $period;
								$date = mktime (0, 0, 0, date("m", $startdate), date("d", $startdate) + $added_days, date("Y", $startdate));
								$storage_units[$date] = $tracker->get_table() . '_' . $date;
								if($this->create_storage_unit($path, '_' . $date))
									$this->create_archive_controller_item($tracker->get_table(), $date, $period, $enddate);
							}
							
						}

						$resultset = $tracker->export($startdate, $enddate, $event);
			
						foreach($resultset as $result)
						{
							if($tracker->is_summary_tracker())
							{
								$this->tdm->create_tracker_item($storage_units[0], $result);
							}
							else
							{
								$date = RepositoryUtilities :: time_from_datepicker($result->get_date());
								
								foreach($storage_units as $start_time => $storage_unit)
								{
									$end_time = mktime (23, 59, 59, date("m", $start_time), date("d", $start_time) + $period, date("Y", $start_time));
									if(($date >= $start_time) && ($date <= $end_time))
									{
										$this->tdm->create_tracker_item($storage_unit, $result);
										break;
									}
								}
							}
							$result->delete();
						}
						echo(Translation :: get('Done') . '<br />');
					}
				}
				echo(Translation :: get('Done') . '<br />');
			}
		}
		
		$adm = AdminDataManager :: get_instance();
		$time = $this->tdm->to_db_date(time());
		$setting = $adm->retrieve_setting_from_variable_name('last_time_archived', 'tracker');
		$setting->set_value($time);
		$setting->update();
		
		// Display the page footer
		$this->parent->display_footer();
	}
	
	/**
	 * Creates a new item in the archive controller table
	 * @param string $tablename the original tablename
	 * @param int startdate the startdate
	 * @param int period the amount of days for 1 table
	 * @param int total_end_date the end date of the archiving process
	 * @return true if creation is valid
	 */
	function create_archive_controller_item($tablename, $startdate, $period, $total_end_date)
	{
		$enddate = mktime (0, 0, 0, date("m", $startdate), date("d", $startdate) + $period, date("Y", $startdate));
		if($enddate > $total_end_date) $enddate = $total_end_date;
		$new_tablename = $tablename . '_' . $startdate;
		
		$controller_item = new ArchiveControllerItem();
		
		$controller_item->set_start_date($startdate);
		$controller_item->set_end_date($enddate);
		$controller_item->set_original_table($tablename);
		$controller_item->set_table_name($new_tablename);
		
		return $controller_item->create();
	}
	
	function create_storage_unit($path, $extra_name)
	{
		$storage_unit_info = Installer :: parse_xml_file($path);
		
		$name = $storage_unit_info['name'] . $extra_name;
		$tables = $this->tdm->get_tables();
		
		$tname = 'tracker_' . $name;
		
		if( in_array($tname,$tables))
		{
			return false;
		}
		
		return $this->tdm->create_storage_unit($name, 
			$storage_unit_info['properties'],$storage_unit_info['indexes']);

	}
}
?>