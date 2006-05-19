<?php
/**
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';

class MonthCalendarLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
{
	/**
	 *
	 */
	private $display_time;
	/**
	 *
	 */
	function set_display_time($time)
	{
		$this->display_time = $time;
	}
	/**
	 *
	 */
	function as_html()
	{
		$calendar_table = new HTML_Table(array ('class' => 'calendar'));
		$m = date('m',$this->display_time);
		$y = date('Y',$this->display_time);
		$first_day = mktime(0, 0, 0, $m, 1, $y);
		$first_day_nr = date('w', $first_day) == 0 ? 6 : date('w', $first_day) - 1;
		$calendar_table->addRow(array ('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'));
		$first_table_date = strtotime('Next Monday',strtotime('-1 Week',$first_day));
		$table_date = $first_table_date;
		$cell = 0;
		while(date('Ym',$table_date) <= date('Ym',$this->display_time))
		{
			do
			{
				$params = array('default_start_date' => $table_date,'default_end_date' => $table_date, LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator','calendaradmin' => '1');
				$add_url = $this->get_url($params);
				$cell_contents = '<a href="'.$add_url.'">'.date('d',$table_date).'</a>';
				$publications = $this->browser->get_calendar_events($table_date,strtotime('+1 Day',$table_date));
				foreach($publications as $index => $publication)
				{
					$cell_contents .= $this->render_publication($publication);
				}
				$calendar_table->setCellContents(intval($cell / 7) + 1, $cell % 7, $cell_contents );
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
					$calendar_table->updateCellAttributes(intval($cell / 7) + 1, $cell % 7,'class="'.implode(' ',$class).'"');
				}
				$cell++;
				$table_date = strtotime('+1 Day',$table_date);
			}
			while($cell%7 != 0);
		}
		$calendar_table->setRowType(0,'th');
		$prev = strtotime('-1 Month',$this->display_time);
		$next = strtotime('+1 Month',$this->display_time);
		$html[] =  '<div style="text-align: center;">';
		$html[] =  '<a href="'.$this->get_url(array('time' => $prev), true).'">&lt;&lt;</a> ';
		$html[] =  date('F Y',$first_day);
		$html[] =  ' <a href="'.$this->get_url(array('time' => $next), true).'">&gt;&gt;</a> ';
		$html[] =  '</div>';
		$html[] = $calendar_table->toHtml();
		return implode("\n",$html);
	}
	function render_publication($publication)
	{
		$event = $publication->get_learning_object();
		$event_url = $this->get_url(array('pid'=>$publication->get_id()), true);
		return '<div class="event"><a href="'.$event_url.'">'.date('H:i',$event->get_start_date()).' '.htmlentities($event->get_title()).'</a></div>';
	}
}
?>