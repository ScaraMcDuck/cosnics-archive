<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';
/**
 * Interval between sections in the week view of the calendar.
 */
define('HOUR_STEP',2);
/**
 * Renderer to display events in a week calendar
 */
class WeekCalendarLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
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
		$week_number = date('W',$this->display_time);
		// Go 1 week back end them jump to the next monday to reach the first day of this week
		$first_day = strtotime('Monday',strtotime('-1 Week',$this->display_time));
		$last_day = strtotime('Sunday',$first_day);
		$calendar_table = new HTML_Table(array ('class' => 'calendar'));
		for($hour = 0; $hour < 24; $hour += HOUR_STEP)
		{
			$cell_content = $hour.'u - '.($hour+HOUR_STEP).'u';
			$calendar_table->setCellContents($hour/HOUR_STEP+1,0,$cell_content);
			for($column = 1; $column <= 7; $column++)
			{
				$day = strtotime('+'.($column-1).' day',$first_day);
				$table_start_date = mktime($hour,0,0,date('m',$day),date('d',$day),date('Y',$day));
				$table_end_date = strtotime('+4 hours',$table_start_date);
				$cell_contents = '';
				// If allowed to add, add a link to the calendar cell
				if($this->is_allowed(ADD_RIGHT))
				{
					$params = array('default_start_date' => $table_start_date,'default_end_date' => $table_end_date,'publish_action' => 'publicationcreator','admin' => '1');
					$add_url = $this->get_url($params);
					$cell_contents = '<div style="text-align:right;"><a href="'.$add_url.'">+</a></div>';
				}
				$publications = $this->browser->get_calendar_events($table_start_date,$table_end_date);
				foreach($publications as $index => $publication)
				{
					$cell_contents .= $this->render_publication($publication,$table_start_date);
				}

				$calendar_table->setCellContents($hour/HOUR_STEP+1,$column,$cell_contents);
			}
		}
		$dates[] = '';
		$today = date('Y-m-d');
		for($day = 0; $day < 7; $day++)
		{
			$week_day = strtotime('+'.$day.' days',$first_day);
			$calendar_table->setCellContents(0,$day+1,get_lang(date('l',$week_day).'Long').'<br/>'.date('Y-m-d',$week_day));
			if($today == date('Y-m-d',$week_day))
			{
				$calendar_table->updateColAttributes($day+1,'class="highlight"');
			}
		}
		$calendar_table->setRowType(0,'th');
		$calendar_table->setColType(0,'th');
		$prev = strtotime('-1 Week',$this->display_time);
		$next = strtotime('+1 Week',$this->display_time);
		$html[] = '<div style="text-align: center;">';
		$html[] =  '<a href="'.$this->get_url(array('time' => $prev), true).'">&lt;&lt;</a> ';
		$html[] =  htmlentities(get_lang('Week')).' '.$week_number.' : '.date('l d M Y',$first_day).' - '.date('l d M Y',strtotime('+6 Days',$first_day));
		$html[] =  ' <a href="'.$this->get_url(array('time' => $next), true).'">&gt;&gt;</a> ';
		$html[] =  '</div>';
		$html[] = $calendar_table->toHtml();
		return implode("\n",$html);
	}
	/**
	 * Renders a publication
	 * @param LearningObjectPublication $publication The publication to render
	 * @param int $table_start_date The current date displayed in the table.
	 */
	function render_publication($publication,$table_start_date)
	{
		static $color_cache;
		$table_end_date = strtotime('+4 hours',$table_start_date);
		$event = $publication->get_learning_object();
		$event_url = $this->get_url(array('pid'=>$publication->get_id()), true);
		$start_date = $event->get_start_date();
		$end_date = $event->get_end_date();
		if($start_date >= $table_end_date || $end_date <= $table_start_date)
		{
			return;
		}
		if(!isset($color_cache[$event->get_id()]))
		{
			$rgb = $this->object2color($event);
			$color_cache[$event->get_id()] = 'rgb('.$rgb['r'].','.$rgb['g'].','.$rgb['b'].')';
		}
		$html[] = '';
		$html[] = '<div class="event" style="border-right: 4px solid '.$color_cache[$event->get_id()].';">';
		if($start_date >= $table_start_date && $start_date < $table_end_date)
		{
			$html[] = date('H:i',$start_date);
		}
		else
		{
			$html[] = '&darr;';
		}
		$html[] = '<a href="'.$event_url.'">'.htmlspecialchars($event->get_title()).'</a>';
		if($end_date > $table_start_date && $end_date <= $table_end_date)
		{
			$html[] = date('H:i',$end_date);
		}
		else
		{
			$html[] = '&darr;';
		}
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>