<?php
/**
 * $Id: monthcalendarlearningobjectpublicationlistrenderer.class.php 11910 2007-04-06 12:50:22Z bmol $
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';
require_once Path :: get_application_library_path().'monthcalendar.class.php';
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
		$table_date = $start_time;
		while($table_date <= $end_time)
		{
			$next_table_date = strtotime('+24 Hours',$table_date);
			$publications = $this->browser->get_calendar_events($table_date,$next_table_date);
			foreach($publications as $index => $publication)
			{
				$cell_contents = $this->render_publication($publication,$table_date);
				$calendar_table->add_event($table_date,$cell_contents );
			}
			$table_date = $next_table_date;
		}
		$url_format = $this->get_url(array('time' => '-TIME-'));
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
		$event = $publication->get_learning_object();
		$start_date = $event->get_start_date();
		$end_date = $event->get_end_date();
		$html[] = '<br /><img src="'.Path :: get_path(WEB_IMG_PATH).'posticon.gif"/>';
		return implode("\n",$html);
	}
}
?>