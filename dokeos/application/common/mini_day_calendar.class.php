<?php

/**
 * $Id: mini_day_calendar.class.php
 * @package application.common
 */
require_once ('day_calendar.class.php');
/**
 * A tabular representation of a day calendar
 */
class MiniDayCalendar extends DayCalendar
{
	private $start_hour;
	
	private $end_hour;
	
	function MiniDayCalendar($display_time, $hour_step = 1)
	{
		parent :: DayCalendar($display_time, $hour_step);
		
		$this->updateAttributes('class="calendar mini"');
	}
	
	function get_start_hour()
	{
		return date('G', strtotime('-4 Hours', $this->get_display_time()));
	}
	
	function get_end_hour()
	{
		return date('G', strtotime('+4 Hours', $this->get_display_time()));
	}
	
	/**
	 * Gets the first date which will be displayed by this calendar.
	 * @return int
	 */
	public function get_start_time()
	{
		return strtotime(date('Y-m-d '. $this->get_start_hour() .':00:00', $this->get_display_time()));
	}
	/**
	 * Gets the end date which will be displayed by this calendar.
	 * @return int
	 */
	public function get_end_time()
	{
		return strtotime(date('Y-m-d '. $this->get_end_hour() .':00:00', $this->get_display_time()));
	}
	
	protected function build_table()
	{
		$start_hour = $this->get_start_hour();
		$end_hour = $this->get_end_hour();
		
		for ($hour = $start_hour; $hour < $end_hour; $hour += $this->get_hour_step())
		{
			$row_id = ($hour / $this->get_hour_step()) - $start_hour;
			
			$table_start_date = mktime($hour, 0, 0, date('m', $this->get_display_time()), date('d', $this->get_display_time()), date('Y', $this->get_display_time()));
			$table_end_date = strtotime('+'.$this->get_hour_step().' hours', $table_start_date);
			$cell_contents = $hour.'u - '. ($hour + $this->get_hour_step()).'u <br />';
			$this->setCellContents($row_id, 0, $cell_contents);
			// Highlight current hour
			if (date('Y-m-d') == date('Y-m-d', $this->get_display_time()))
			{
				if (date('H') >= $hour && date('H') < $hour + $this->get_hour_step())
				{
					$this->updateCellAttributes($row_id, 0, 'class="highlight"');
				}
			}
			// Is current table hour during working hours?
			if ($hour < 8 || $hour > 18)
			{
				$this->updateCellAttributes($row_id, 0, 'class="disabled_month"');
			}
		}
	}
	
	/**
	 * Returns a html-representation of this minidaycalendar
	 * @return string
	 */
	public function toHtml()
	{
		$html = parent :: toHtml();
		$html = str_replace('class="calendar_navigation"', 'class="calendar_navigation mini"', $html);
		return $html;
	}
}
?>