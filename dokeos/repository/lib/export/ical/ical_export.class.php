<?php

require_once dirname(__FILE__).'/../learning_object_export.class.php';
require_once Path :: get_plugin_path() . 'icalcreator/iCalcreator.class.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class IcalExport extends LearningObjectExport
{
	function IcalExport($learning_object)
	{
		parent :: __construct($learning_object);	
	}
	
	public function export_learning_object()
	{
		$learning_object = $this->get_learning_object();
		$file = Path :: get(SYS_TEMP_PATH). $learning_object->get_owner_id() . '/export_ical_' . $learning_object->get_id() . '.ics';
        
        // TODO: Get language isocode for iCal export
        //define('ICAL_LANG',api_get_language_isocode());
        

        $ical = new vcalendar();
        $ical->setConfig('unique_id', Path :: get(WEB_PATH));
        $ical->setProperty('method', 'PUBLISH');
        $ical->setConfig('url', Path :: get(WEB_PATH));
        
        $vevent = new vevent();
        $vevent->setProperty('summary', mb_convert_encoding($learning_object->get_title(), 'UTF-8'));

        $vevent->setProperty('dtstart', $this->get_date_as_array($learning_object->get_start_date()));
        $vevent->setProperty('dtend', $this->get_date_as_array($learning_object->get_end_date()));
        
        //$vevent->setProperty('LOCATION', $learning_object->get_location());
        $vevent->setProperty('description', mb_convert_encoding($learning_object->get_description(), 'UTF-8'));

        $owner = UserDataManager :: get_instance()->retrieve_user($learning_object->get_owner_id());
        
        $vevent->setProperty('organizer', $owner->get_email());
        $vevent->setProperty('attendee', $owner->get_email());

        $ical->setComponent($vevent);
        
        $calendar = $ical->createCalendar();

        $handle = fopen($file, 'w+');
        fwrite($handle, $calendar);
        fclose($handle);
      
      	return $file;
	}
	
	function get_date_as_array($date)
	{
		$y = date('Y', $date);
        $m = date('m', $date);
        $d = date('d', $date);
        $h = date('H', $date);
        $M = date('i', $date);
        $s = date('s', $date);
        
        return array('year' => $y, 'month' => $m, 'day' => $d, 'hour' => $h, 'min' => $M, 'sec' => $s);
	}

}
?>