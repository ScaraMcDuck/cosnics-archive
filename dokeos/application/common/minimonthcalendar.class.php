<?php


/**
 * $Id: monthcalendarlearningobjectpublicationlistrenderer.class.php 10541 2006-12-21 10:08:16Z bmol $
 * @package application.common
 */
require_once ('monthcalendar.class.php');
/**
 * A tabular representation of a month calendar which can be used to navigate a
 * calendar tool.
 */
class MiniMonthCalendar extends MonthCalendar
{
	const PERIOD_MONTH = 0;
	const PERIOD_WEEK = 1;
	const PERIOD_DAY = 2;
	public function MiniMonthCalendar($display_time)
	{
		parent :: MonthCalendar($display_time);
		$this->set_daynames(array ('M', 'D', 'W', 'D', 'V', 'Z', 'Z'));
		$this->updateAttributes('class="calendar mini"');
		$this->setRowType(0, 'th');
	}
	public function add_navigation_links($url_format)
	{
		$day = $this->get_start_time();
		$row = 1;
		$max_rows = $this->getRowCount();
		while ($row < $max_rows)
		{
			for ($col = 0; $col < 7; $col ++)
			{
				$url = str_replace('-TIME-', $day, $url_format);
				$content = $this->getCellContents($row, $col);
				$content = '<a href="'.$url.'">'.$content.'</a>';
				$this->setCellContents($row, $col, $content);
				$day = strtotime('+24 Hours', $day);
			}
			$row ++;
		}

	}
	public function mark_period($period)
	{
		switch ($period)
		{
			case self :: PERIOD_MONTH :
				break;
			case self :: PERIOD_WEEK :
				$monday = $this->get_start_time();
				$row = 1;
				while ($this->get_display_time() > strtotime('Next Monday', $monday))
				{
					$monday = strtotime('Next Monday', $monday);
					$row ++;
				}
				$this->updateRowAttributes($row, 'style="border: 2px solid black;"', true);
				break;
			case self :: PERIOD_DAY :
				$day = strtotime(date('Y-m-d 00:00:00',$this->get_start_time()));
				$date_diff = (strtotime(date('Y-m-d 00:00:00',$this->get_display_time())) - $day) / (60*60*24);
				$cell = 7 + $date_diff;
				$this->updateCellAttributes($cell / 7, $cell % 7, 'style="border: 2px solid black;"', true);
				break;
		}
	}
	public function toHtml()
	{
		$html = parent :: toHtml();
		$html = str_replace('class="calendar_navigation"', 'class="calendar_navigation mini"', $html);
		return $html;
	}
}
?>