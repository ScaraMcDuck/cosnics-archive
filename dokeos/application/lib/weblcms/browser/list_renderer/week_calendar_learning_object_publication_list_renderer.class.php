<?php
/**
 * $Id: weekcalendarlearning_object_publication_list_renderer.class.php 15420 2008-05-26 17:34:32Z Scara84 $
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learning_object_publication_list_renderer.class.php';
require_once Path :: get_application_library_path().'week_calendar.class.php';
/**
 * Interval between sections in the week view of the calendar.
 */
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
		$calendar_table = new WeekCalendar($this->display_time);
		$start_time = $calendar_table->get_start_time();
		$end_time = $calendar_table->get_end_time();
		
		$publications = $this->browser->get_calendar_events($start_time, $end_time);
		
		$table_date = $start_time;
		while($table_date <= $end_time)
		{
			$next_table_date = strtotime('+'.$calendar_table->get_hour_step().' Hours',$table_date);
			
			foreach ($publications as $index => $publication)
			{
				$object = $publication->get_learning_object();
				
				$start_date = $object->get_start_date();
				$end_date = $object->get_end_date();
				
				if ($table_date < $start_date && $start_date < $next_table_date || $table_date < $end_date && $end_date < $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
				{
					$cell_contents = $this->render_publication($publication,$table_date, $calendar_table->get_hour_step());
					$calendar_table->add_event($table_date, $cell_contents);
				}
			}
			
			$table_date = $next_table_date;
		}
		$url_format = $this->get_url(array('time' => '-TIME-', 'view' => $_GET['view']));
		$calendar_table->add_calendar_navigation($url_format);
		$html[] = $calendar_table->render();
		return implode("\n",$html);
	}
	/**
	 * Renders a publication
	 * @param LearningObjectPublication $publication The publication to render
	 * @param int $table_start_date The current date displayed in the table.
	 */
	function render_publication($publication,$table_start_date, $calendar_hour_step)
	{
		static $color_cache;
		$table_end_date = strtotime('+'. $calendar_hour_step .'  hours',$table_start_date);
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