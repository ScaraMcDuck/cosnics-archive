<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';
/**
 * Renderer to display events in a month calendar
 */
class MonthCalendarLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
{
	/**
	 * The current time displayed in the calendar
	 */
	private $display_time;
	/**
	 * Sets the current display time.
	 * @param int $time The current display time.
	 */
	function set_display_time($time)
	{
		$this->display_time = $time;
	}
	/**
	 * Returns the HTML output of this renderer.
	 * @return string The HTML output
	 */
	function as_html()
	{
		$calendar_table = new HTML_Table(array ('class' => 'calendar'));
		$m = date('m',$this->display_time);
		$y = date('Y',$this->display_time);
		$first_day = mktime(0, 0, 0, $m, 1, $y);
		$first_day_nr = date('w', $first_day) == 0 ? 6 : date('w', $first_day) - 1;
		$calendar_table->addRow(array (get_lang('MondayLong'), get_lang('TuesdayLong'), get_lang('WednesdayLong'), get_lang('ThursdayLong'), get_lang('FridayLong'), get_lang('SaturdayLong'), get_lang('SundayLong')));
		$first_table_date = strtotime('Next Monday',strtotime('-1 Week',$first_day));
		$table_date = $first_table_date;
		$cell = 0;
		while(date('Ym',$table_date) <= date('Ym',$this->display_time))
		{
			do
			{
				$params = array('default_start_date' => $table_date,'default_end_date' => $table_date, LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator','admin' => '1');
				$add_url = $this->get_url($params);
				$cell_contents = '<a href="'.$add_url.'">'.date('d',$table_date).'</a>';
				$publications = $this->browser->get_calendar_events($table_date,strtotime('+1 Day',$table_date));
				foreach($publications as $index => $publication)
				{
					$cell_contents .= $this->render_publication($publication,$table_date);
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
		$html[] =  get_lang(date('F',$first_day).'Long').' '.date('Y',$first_day);
		$html[] =  ' <a href="'.$this->get_url(array('time' => $next), true).'">&gt;&gt;</a> ';
		$html[] =  '</div>';
		$html[] = $calendar_table->toHtml();
		return implode("\n",$html);
	}
	/**
	 * Renders a publication
	 * @param LearningObjectPublication $publication The publication to render
	 * @param int $table_date The current date displayed in the table.
	 */
	function render_publication($publication,$table_date)
	{
		static $color_cache;
		$event = $publication->get_learning_object();
		$event_url = $this->get_url(array('pid'=>$publication->get_id()), true);
		$start_date = $event->get_start_date();
		$end_date = $event->get_end_date();
		if(!isset($color_cache[$event->get_id()]))
		{
			$color_cache[$event->get_id()] = 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')';
		}
		$html[] = '';
		$html[] = '<div class="event" style="border-right: 4px solid '.$color_cache[$event->get_id()].';">';
		if($start_date > $table_date && $start_date <= strtotime('+1 Day',$table_date))
		{
			$html[] = date('H:i',$start_date);
		}
		else
		{
			$html[] = '&rarr;';
		}
		$html[] = '<a href="'.$event_url.'">'.htmlentities($event->get_title()).'</a>';
		if($end_date >= $table_date && $end_date < strtotime('+1 Day',$table_date))
		{
			$html[] = date('H:i',$end_date);
		}
		else
		{
			$html[] = '&rarr;';
		}
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>