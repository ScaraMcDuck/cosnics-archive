<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';
define('HOUR_STEP',3);
/**
 * Renderer to display a list of events.
 */
class DayCalendarLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
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
		for($hour = 0; $hour < 24; $hour += HOUR_STEP)
		{
			$table_start_date = mktime($hour,0,0,date('m',$this->display_time),date('d',$this->display_time),date('Y',$this->display_time));
			$table_end_date = strtotime('+'.HOUR_STEP.' hours',$table_start_date);
			$params = array('default_start_date' => $table_start_date,'default_end_date' => $table_end_date,'publish_action' => 'publicationcreator','admin' => '1');
			$add_url = $this->get_url($params);
			$cell_contents = '<a href="'.$add_url.'">'.$hour.'u - '.($hour+HOUR_STEP).'u'.'</a>';
			$publications = $this->browser->get_calendar_events($table_start_date,$table_end_date);
			foreach($publications as $index => $publication)
			{
				$cell_contents .= $this->render_publication($publication,$table_start_date);
			}
			$calendar_table->setCellContents($hour/HOUR_STEP,0,$cell_contents);
		}
		$prev = strtotime('-1 Day',$this->display_time);
		$next = strtotime('+1 Day',$this->display_time);
		$html[] = '<div style="text-align: center;">';
		$html[] =  '<a href="'.$this->get_url(array('time' => $prev), true).'">&lt;&lt;</a> ';
		$html[] =  date('l d F Y',$this->display_time);
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
		$table_end_date = strtotime('+'.HOUR_STEP.' hours',$table_start_date);
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
			$color_cache[$event->get_id()] = 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')';
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
		$html[] = '<a href="'.$event_url.'">'.htmlentities($event->get_title()).'</a>';
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