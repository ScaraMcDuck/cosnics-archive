<?php
/**
 * $Id: monthcalendarlearning_object_publication_list_renderer.class.php 11910 2007-04-06 12:50:22Z bmol $
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learning_object_publication_list_renderer.class.php';
require_once Path :: get_application_library_path().'mini_month_calendar.class.php';
require_once Path :: get_application_library_path().'month_calendar.class.php';
/**
 * Renderer to display events in a month calendar
 */
class MiniMonthCalendarLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
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
		$calendar_table = new MiniMonthCalendar($this->display_time);
		$start_time = $calendar_table->get_start_time();
		$end_time = $calendar_table->get_end_time();
		
		$publications = $this->browser->get_calendar_events($start_time, $end_time);
		
		$table_date = $start_time;
		while($table_date <= $end_time)
		{
			$next_table_date = strtotime('+24 Hours',$table_date);
			
			foreach ($publications as $index => $publication)
			{
				if (!$calendar_table->contains_events_for_time($table_date))
				{
					$object = $publication->get_learning_object();
					
					$start_date = $object->get_start_date();
					$end_date = $object->get_end_date();
					
					if ($table_date < $start_date && $start_date < $next_table_date || $table_date <= $end_date && $end_date <= $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
					{
						$cell_contents = $this->render_publication($publication,$table_date);
						$calendar_table->add_event($table_date,$cell_contents );
					}
				}
			}
			
			$table_date = $next_table_date;
		}
		$url_format = $this->get_url(array('time' => '-TIME-', 'view' => $_GET['view']));
		$calendar_table->add_calendar_navigation($url_format);
		switch($this->browser->get_parameter('view'))
		{
			case 'month':
				$calendar_table->mark_period(MiniMonthCalendar::PERIOD_MONTH);
				break;
			case 'week':
				$calendar_table->mark_period(MiniMonthCalendar::PERIOD_WEEK);
				break;
			case 'day':
				$calendar_table->mark_period(MiniMonthCalendar::PERIOD_DAY);
				break;
		}
		$calendar_table->add_navigation_links($url_format);
		$html[] = $calendar_table->render();
		return implode("\n",$html);
	}
	/**
	 * Renders a publication
	 * @param LearningObjectPublication $publication The publication to render
	 * @param int $table_date The current date displayed in the table.
	 */
	function render_publication($publication,$table_date)
	{
		$event = $publication->get_learning_object();
		$start_date = $event->get_start_date();
		$end_date = $event->get_end_date();
		$html[] = '<br /><img src="'.Theme :: get_common_image_path().'action_posticon.png"/>';
		return implode("\n",$html);
	}
}
?>