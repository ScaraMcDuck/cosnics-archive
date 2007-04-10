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
				$day = strtotime('Next Day', $day);
				$url = str_replace('-TIME-', $day, $url_format);
				$content = $this->getCellContents($row, $col);
				$content = '<a href="'.$url.'">'.$content.'</a>';
				$this->setCellContents($row, $col, $content);
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
			$monday = $this->get_start_time();
			$cell = 7;
			while ($this->get_display_time() > strtotime('+24 Hours', $monday))
			{
				$monday = strtotime('+24 Hours', $monday);
				$cell ++;
			}
			$this->updateCellAttributes($cell / 7, $cell % 7, 'style="border: 2px solid black;"', true);
			break;
	}
}
}
?>