<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_renderer.class.php');
require_once (Path :: get_application_library_path().'day_calendar.class.php');
/**
 * This personal calendar renderer provides a tabular day view of the events in
 * the calendar.
 */
class PersonalCalendarDayRenderer extends PersonalCalendarRenderer
{
	/**
	 * @see PersonalCalendarRenderer::render()
	 */
	public function render()
	{
		$calendar = new DayCalendar($this->get_time(), 2);
		$from_date = strtotime(date('Y-m-d 00:00:00', $this->get_time()));
		$to_date = strtotime(date('Y-m-d 23:59:59', $this->get_time()));
		$events = $this->get_events($from_date, $to_date);
		$dm = RepositoryDataManager :: get_instance();
		$html = array ();
		foreach ($events as $index => $event)
		{
			$content = $this->render_event($event);
			$calendar->add_event($event->get_start_date(), $content);		
		}
		$parameters['time'] = '-TIME-';
		$calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
		$html = $calendar->toHtml();
		$html .= $this->build_legend();
		return $html;
	}
	
	/**
	 * Gets a html representation of a calendar event
	 * @param PersonalCalendarEvent $event
	 * @return string
	 */
	private function render_event($event)
	{
		$html[] = '<div class="event" style="border-left: 5px solid '.$this->get_color(Translation :: get($event->get_source())).';">';
		$html[] = '<a href="'.$event->get_url().'">';
		$html[] = date('H:i', $event->get_start_date).' '.htmlspecialchars($event->get_title());
		$html[] = '</a>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
}
?>