<?php
/**
 * @package export
 */
require_once dirname(__FILE__).'/../learning_object_import.class.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class IcalImport extends LearningObjectImport
{
	function IcalImport($learning_object_file, $user, $category)
	{
		parent :: __construct($learning_object_file, $user, $category);	
	}
	
	public function import_learning_object()
	{
		$file = $this->get_learning_object_file();
		
		$content = file_get_contents($file['tmp_name']);
		$lines = explode("\n", $content);
		$count = count($lines);
		
		for($i = 0; $i < $count; $i++)
		{
			$line = $lines[$i];
			if($line == 'BEGIN:VEVENT')
			{
				$i = $this->import_event($lines, $i, $count);
			}
		}
    	
		return $this->calendar_event_ids;
	}
	
	private $calendar_event_ids;
	
	public function import_event($lines, $i, $count)
	{
		$calendar_event = new CalendarEvent();
		$calendar_event->set_owner_id($this->get_user()->get_id());
		$calendar_event->set_parent_id($this->get_category());
		
		for($i; $i < $count; $i++)
		{
			$line = $lines[$i];	
			if($line == 'END:VEVENT')
			{
				break;
			}
			
			if(substr($line, 0, 7) == 'SUMMARY')
			{
				$calendar_event->set_title(substr($line, 8));
			}
			
			if(substr($line, 0, 11) == 'DESCRIPTION')
			{
				$calendar_event->set_description(substr($line, 12));
			}
			
			if(substr($line, 0, 7) == 'DTSTART')
			{
				$start = substr($line, 8);
				$time = strtotime($start);
				$calendar_event->set_start_date($time);
			}
			
			if(substr($line, 0, 5) == 'DTEND')
			{
				$end = substr($line, 6);
				$time = strtotime($end);
				$calendar_event->set_end_date($time);
			}

		}
		
		$calendar_event->create();
		$this->calendar_event_ids[] = $calendar_event->get_id();
		
		return $i;
	}
	
	
}
?>