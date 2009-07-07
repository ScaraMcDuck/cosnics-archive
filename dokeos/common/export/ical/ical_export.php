<?php
/**
 * $Id: filecompression.class.php 13555 2007-10-24 14:15:23Z bmol $
 * @package export
 */
require_once Path :: get_library_path() . 'export/export.class.php';
require_once Path :: get_plugin_path() . 'icalcreator/iCalcreator.class.php';
/**
 * Exports data to iCal-format
 * 
 * Expected format
 * $row['title']
 * $row['date']
 * $row['enddate']
 * $row['location']
 * $row['text']
 * $row['mail']
 * 
 */
class IcalExport extends Export
{

    public function write_to_file($data)
    {
        $file = Filesystem :: create_unique_name($this->get_path(SYS_ARCHIVE_PATH), $this->get_filename());
        
        // TODO: Get language isocode for iCal export
        //define('ICAL_LANG',api_get_language_isocode());
        

        $ical = new vcalendar();
        $ical->setConfig('unique_id', Path :: get(WEB_PATH));
        $ical->setProperty('method', 'PUBLISH');
        $ical->setConfig('url', Path :: get(WEB_PATH));
        
        foreach ($data as $index => $row)
        {
            $vevent = new vevent();
            $vevent->setProperty('summary', mb_convert_encoding($row['title'], 'UTF-8'));
            
            if (empty($row['date']))
            {
                header('location:' . $_SERVER['REFERER_URI']);
            }
            
            list($y, $m, $d, $h, $M, $s) = preg_split('/[\s:-]/', $row['date']);
            $vevent->setProperty('dtstart', array('year' => $y, 'month' => $m, 'day' => $d, 'hour' => $h, 'min' => $M, 'sec' => $s));
            
            if (empty($row['enddate']))
            {
                $y2 = $y;
                $m2 = $m;
                $d2 = $d;
                $h2 = $h;
                $M2 = $M + 15;
                $s2 = $s;
                if ($M2 > 60)
                {
                    $M2 = $M2 - 60;
                    $h2 += 1;
                }
            }
            else
            {
                list($y2, $m2, $d2, $h2, $M2, $s2) = preg_split('/[\s:-]/', $row['enddate']);
            }
            $vevent->setProperty('dtend', array('year' => $y2, 'month' => $m2, 'day' => $d2, 'hour' => $h2, 'min' => $M2, 'sec' => $s2));
            
            $vevent->setProperty('LOCATION', $row['location']);
            $vevent->setProperty('description', mb_convert_encoding($row['text'], 'UTF-8'));
            //$vevent->setProperty( 'comment', 'This is a comment' );
            $vevent->setProperty('organizer', $row['mail']);
            $vevent->setProperty('attendee', $row['mail']);
            //$vevent->setProperty( 'rrule', array( 'FREQ' => 'WEEKLY', 'count' => 4));// occurs also four next weeks
            

            $ical->setComponent($vevent);
        }
        
        $calendar = $ical->createCalendar();
        
        $handle = fopen($file, 'a+');
        fwrite($handle, $calendar);
        fclose($handle);
        
        Filesystem :: file_send_for_download($file, true, $file);
        exit();
    }
}
?>
