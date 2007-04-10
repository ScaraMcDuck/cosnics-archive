<?php
/**
 * $Id: monthcalendarlearningobjectpublicationlistrenderer.class.php 10541 2006-12-21 10:08:16Z bmol $
 * @package application.common
 */
require_once ('HTML/Table.php');
/**
 * A tabular representation of a month calendar
 */
class MonthCalendar extends HTML_Table
{
	/**
	 * A time in the month represented by this calendar
	 */
	private $display_time;
	/**
	 * Keep mapping of dates and their corresponding table cells
	 */
	private $cell_mapping;
	/**
	 * The navigation links
	 */
	private $navigation_html;
	/**
	 * Creates a new month calendar
	 * @param int $display_time A time in the month to be displayed
	 */
	function MonthCalendar($display_time)
	{
		$this->navigation_html = '';
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
	 *
	 */
	public function get_display_time()
	{
		return $this->display_time;
	}
	/**
	 * Gets the first date which will be displayed by this calendar. This is
	 * always a monday. If the current month doesn't start on a monday, the last
	 * monday of previous month is returned.
	 * @return int
	 */
	public function get_start_time()
	{
		$first_day = mktime(0, 0, 0, date('m',$this->display_time), 1, date('Y',$this->display_time));
		return strtotime('Next Monday',strtotime('-1 Week',$first_day));
	}
	/**
	 * Gets the end date which will be displayed by this calendar. This is
	 * always a sunday. Of the current month doesn't end on a sunday, the first
	 * sunday of next month is returned.
	 * @return int
	 */
	public function get_end_time()
	{
		$end_time = $this->get_start_time();
		while(date('Ym',$end_time) <= date('Ym',$this->display_time))
		{
			$end_time = strtotime('+1 Week',$end_time);
		}
		return $end_time;
	}
	/**
	 * Builds the table
	 */
	private function build_table()
	{
		$first_day = mktime(0, 0, 0, date('m',$this->display_time), 1, date('Y',$this->display_time));
		$first_day_nr = date('w', $first_day) == 0 ? 6 : date('w', $first_day) - 1;
		$this->addRow(array (get_lang('MondayLong'), get_lang('TuesdayLong'), get_lang('WednesdayLong'), get_lang('ThursdayLong'), get_lang('FridayLong'), get_lang('SaturdayLong'), get_lang('SundayLong')));
		$this->setRowType(0,'th');
		$first_table_date = strtotime('Next Monday',strtotime('-1 Week',$first_day));
		$table_date = $first_table_date;
		$cell = 0;
		while(date('Ym',$table_date) <= date('Ym',$this->display_time))
		{
			do
			{
				$cell_contents = date('d',$table_date);
				$row = intval($cell / 7) + 1;
				$column =  $cell % 7;
				$this->setCellContents($row,$column , $cell_contents );
				$this->cell_mapping[date('Ymd',$table_date)] = array($row,$column);
				$class = array();
				// Is current table date today?
				if(date('Ymd',$table_date) == date('Ymd'))
				{
					$class[] = 'highlight';
				}
				// If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
				if(date('w',$table_date)%6 == 0)
				{
					$class[] = 'weekend';
				}
				// Is current table date in this month or another one?
				if( date('Ym',$table_date) != date('Ym',$this->display_time))
				{
					$class[] = 'disabled_month';
				}
				if(count($class) > 0)
				{
					$this->updateCellAttributes(intval($cell / 7) + 1, $cell % 7,'class="'.implode(' ',$class).'"');
				}
				$cell++;
				$table_date = strtotime('+1 Day',$table_date);
			}
			while($cell%7 != 0);
		}
		$this->setRowType(0,'th');
	}
	/**
	 * Add an event to the calendar
	 * @param int $time A time in the day on which the event should be displayed
	 * @param string $content The html content to insert in the month calendar
	 */
	public function add_event($time,$content)
	{
		$cell_mapping_key = date('Ymd',$time);
		$row = $this->cell_mapping[$cell_mapping_key][0];
		$column = $this->cell_mapping[$cell_mapping_key][1];
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
		$prev = strtotime('-1 Month',$this->display_time);
		$next = strtotime('+1 Month',$this->display_time);
		$navigation = new HTML_Table('class="calendar_navigation"');
		$navigation->updateCellAttributes(0,0,'style="text-align: left;"');
		$navigation->updateCellAttributes(0,1,'style="text-align: center;"');
		$navigation->updateCellAttributes(0,2,'style="text-align: right;"');
		$navigation->setCellContents(0,0,'<a href="'.str_replace('-TIME-',$prev,$url_format).'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/prev.png" style="vertical-align: middle;" alt="&lt;&lt;"/></a> ');
		$navigation->setCellContents(0,1,get_lang(date('F',$this->display_time).'Long').' '.date('Y',$this->display_time));
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
			$this->setCellContents(0,$day,$daynames[$day]);
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