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
		$daynames[] = get_lang('MondayShort');
		$daynames[] = get_lang('TuesdayShort');
		$daynames[] = get_lang('WednesdayShort');
		$daynames[] = get_lang('ThursdayShort');
		$daynames[] = get_lang('FridayShort');
		$daynames[] = get_lang('SaturdayShort');
		$daynames[] = get_lang('SundayShort');
		$this->set_daynames($daynames);
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
				$rows = $this->getRowCount();
				$top_row = 'style="border-left: 2px solid black;border-right: 2px solid black;border-top: 2px solid black;"';
				$middle_row = 'style="border-left: 2px solid black;border-right: 2px solid black;"';
				$bottom_row = 'style="border-left: 2px solid black;border-right: 2px solid black;border-bottom: 2px solid black;"';
				for($row = 1; $row < $rows; $row++)
				{
					switch($row)
					{
						case 1:
							$style = $top_row;
							break;
						case $rows-1:
							$style = $bottom_row;
							break;
						default:
							$style = $middle_row;
							break;
					}
					$this->updateRowAttributes($row,$style,true);
				}
				break;
			case self :: PERIOD_WEEK :
				$monday = $day = strtotime(date('Y-m-d 00:00:00', $this->get_start_time()));
				$this_week = strtotime(date('Y-m-d 00:00:00', $this->get_display_time()));
				$week_diff = floor(($this_week - $monday) / (60 * 60 * 24 * 7));
				$row = 1 + $week_diff;
				$this->updateRowAttributes($row, 'style="border: 2px solid black;"', true);
				break;
			case self :: PERIOD_DAY :
				$day = strtotime(date('Y-m-d 00:00:00', $this->get_start_time()));
				$today = $this->get_display_time();
				$date_diff = floor(($today - $day) / (60 * 60 * 24));
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