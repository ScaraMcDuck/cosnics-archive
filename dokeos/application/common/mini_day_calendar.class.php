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
	function MiniDayCalendar($display_time, $hour_step = 1)
	{
		parent :: DayCalendar($display_time, $hour_step);
		$this->updateAttributes('class="calendar mini"');
	}
	
	protected function build_table()
	{
		for ($hour = 0; $hour < 24; $hour += $this->get_hour_step())
		{
			$table_start_date = mktime($hour, 0, 0, date('m', $this->get_display_time()), date('d', $this->get_display_time()), date('Y', $this->get_display_time()));
			$table_end_date = strtotime('+'.$this->get_hour_step().' hours', $table_start_date);
			$cell_contents = $hour.'u - '. ($hour + $this->get_hour_step()).'u <br />';
			$this->setCellContents($hour / $this->get_hour_step(), 0, $cell_contents);
			// Highlight current hour
			if (date('Y-m-d') == date('Y-m-d', $this->get_display_time()))
			{
				if (date('H') >= $hour && date('H') < $hour + $this->get_hour_step())
				{
					$this->updateCellAttributes($hour / $this->get_hour_step(), 0, 'class="highlight"');
				}
			}
			// Is current table hour during working hours?
			if ($hour < 8 || $hour > 18)
			{
				$this->updateCellAttributes($hour / $this->get_hour_step(), 0, 'class="disabled_month"');
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