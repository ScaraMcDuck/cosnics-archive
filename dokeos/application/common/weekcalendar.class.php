<?php
/**
 * $Id: monthcalendarlearningobjectpublicationlistrenderer.class.php 10541 2006-12-21 10:08:16Z bmol $
 * @package application.common
 */
require_once ('HTML/Table.php');
/**
 * A tabular representation of a week calendar
 */
class WeekCalendar extends HTML_Table
{
	/**
	 * A time in the week represented by this calendar
	 */
	private $display_time;
	/**
	 * The navigation links
	 */
	private $navigation_html;
	/**
	 * The number of hours for one table cell.
	 */
	private $hour_step;
	/**
	 * Creates a new week calendar
	 * @param int $display_time A time in the week to be displayed
	 * @param int $hour_step The number of hours for one table cell. Defaults to
	 * 2.
	 */
	function WeekCalendar($display_time,$hour_step = 2)
	{
		$this->navigation_html = '';
		$this->hour_step = $hour_step;
		if (is_null($display_time))
		{
			$display_time = time();
		}
		$this->display_time = $display_time;
		parent::HTML_Table(array('class'=>'calendar'));
		$cell_mapping = array();
		$this->build_table();
	}
	/**
	 * Gets the number of hours for one table cell.
	 * @return int
	 */
	public function get_hour_step()
	{
		return $this->hour_step;
	}
	/**
	 * Gets the first date which will be displayed by this calendar. This is
	 * always a monday.
	 * @return int
	 */
	public function get_start_time()
	{
		return strtotime('Next Monday',strtotime('-1 Week',$this->display_time));
	}
	/**
	 * Gets the end date which will be displayed by this calendar. This is
	 * always a sunday.
	 * @return int
	 */
	public function get_end_time()
	{
		return strtotime('Next Sunday',$this->get_start_time());
	}
	/**
	 * Builds the table
	 */
	private function build_table()
	{
		$week_number = date('W',$this->display_time);
		// Go 1 week back end them jump to the next monday to reach the first day of this week
		$first_day = $this->get_start_time();
		$last_day = $this->get_end_time;
		for($hour = 0; $hour < 24; $hour += $this->hour_step)
		{
			$cell_content = $hour.'u - '.($hour+$this->hour_step).'u';
			$this->setCellContents($hour/$this->hour_step+1,0,$cell_content);
			for($column = 1; $column <= 7; $column++)
			{
				$day = strtotime('+'.($column-1).' day',$first_day);
				$table_start_date = mktime($hour,0,0,date('m',$day),date('d',$day),date('Y',$day));
				$table_end_date = strtotime('+4 hours',$table_start_date);
				$cell_contents = '';
			}
		}
		$dates[] = '';
		$today = date('Y-m-d');
		for($day = 0; $day < 7; $day++)
		{
			$week_day = strtotime('+'.$day.' days',$first_day);
			$this->setCellContents(0,$day+1,get_lang(date('l',$week_day).'Long').'<br/>'.date('Y-m-d',$week_day));
			for($hour = 0; $hour < 24; $hour += $this->hour_step)
			{
				$class = array();
				if($today == date('Y-m-d',$week_day))
				{
					if(date('H') >= $hour && date('H') < $hour+$this->hour_step)
					{
						$class[] = 'highlight';
					}
				}
				// If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
				if(date('w',$week_day)%6 == 0)
				{
					$class[] = 'weekend';
				}
				if(count($class) > 0)
				{
					$this->updateCellAttributes($hour/$this->hour_step+1,$day+1,'class="'.implode(' ',$class).'"');
				}
			}
		}
		$this->setRowType(0,'th');
		$this->setColType(0,'th');
	}
	/**
	 * Add an event to the calendar
	 * @param int $time A time in the day on which the event should be displayed
	 * @param string $content The html content to insert in the month calendar
	 */
	public function add_event($time,$content)
	{
		$row = date('H',$time)/$this->hour_step+1;
		$column = date('w',$time);
		if($column == 0)
		{
			$column = 7;
		}
		$cell_content = $this->getCellContents($row,$column);
		$cell_content .= $content;
		$this->setCellContents($row,$column, $cell_content );
	}
	/**
	 * Adds a navigation bar to the calendar
	 * @param string $url_format The *TIME* in this string will be replaced by a
	 * timestamp
	 */
	public function add_calendar_navigation($url_format)
	{
		$week_number = date('W',$this->display_time);
		$prev = strtotime('-1 Week',$this->display_time);
		$next = strtotime('+1 Week',$this->display_time);
		$navigation = new HTML_Table('class="calendar_navigation"');
		$navigation->updateCellAttributes(0,0,'style="text-align: left;"');
		$navigation->updateCellAttributes(0,1,'style="text-align: center;"');
		$navigation->updateCellAttributes(0,2,'style="text-align: right;"');
		$navigation->setCellContents(0,0,'<a href="'.str_replace('-TIME-',$prev,$url_format).'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/prev.png" style="vertical-align: middle;" alt="&lt;&lt;"/></a> ');
		$navigation->setCellContents(0,1,htmlentities(get_lang('Week')).' '.$week_number.' : '.date('l d M Y',$this->get_start_time()).' - '.date('l d M Y',strtotime('+6 Days',$this->get_start_time())));
		$navigation->setCellContents(0,2,' <a href="'.str_replace('-TIME-',$next,$url_format).'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/next.png" style="vertical-align: middle;" alt="&gt;&gt;"/></a> ');
		$this->navigation_html = $navigation->toHtml();
	}
	/**
	 * Sets the daynames.
	 * If you don't use this function, the long daynames will be displayed
	 * @param array $daynames An array of 7 elements with keys 0 -> 6 containing
	 * the titles to display.
	 */
	public function set_daynames($daynames)
	{
		for($day = 0; $day < 7; $day++)
		{
			$this->setCellContents(0,$day+1,$daynames[$day]);
		}
	}
	/**
	 * Returns a html-representation of this monthcalendar
	 * @return string
	 */
	public function toHtml()
	{
		$html = parent::toHtml();
		return $this->navigation_html.$html;
	}
}
?>