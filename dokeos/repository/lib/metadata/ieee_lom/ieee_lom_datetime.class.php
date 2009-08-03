<?php
require_once (dirname(__FILE__) . '/ieee_lom_time.class.php');

class IeeeLomDateTime extends IeeeLomTime
{
    public function IeeeLomDateTime($timestamp = null, $description = null)
    {
        parent :: IeeeLomTime($timestamp, $description);
    }
    
	/**
     * Return an ISO 8601 formatted datetime
     *
     * @return string Formatted datetime
     */
    public function get_datetime()
    {
        $datetime_str = '';
        $time_str     = '';
        
        if(isset($this->day) && isset($this->month) && isset($this->year))
        {
            $datetime_str = DatetimeTool :: get_complete_year($this->year) . '-' . $this->get_month(true) . '-' . $this->get_day(true);
        }
        
        $hour = $this->get_hour(true);
        if(isset($hour))
        {
            $time_str = 'T' . $hour;
        }
        
        $min = $this->get_min(true);
        if(isset($min))
        {
            if(strlen($time_str) > 0)
            {
                $time_str .= ':' . $min;
            }
            else
            {
                $time_str = 'T00:' . $min;
            }
        }
        
        $sec = $this->get_sec(true);
        if(isset($sec))
        {
            if(strlen($time_str) > 0)
            {
                $time_str .= ':' . $sec;
            }
            else
            {
                $time_str = 'T00:00:' . $sec;
            }
        }
        
        if(strlen($time_str) > 0)
        {
            $datetime_str .= $time_str;
        }
        
        return $datetime_str;
    }
    
    /**
     * Set the the instance datetime value
     * 
     * @param $date_string Date in format accepted by strtotime() 
     */
    public function set_datetime_from_string($date_string)
    {
        $this->set_timestamp(strtotime($date_string));
    }
    
} 
?>