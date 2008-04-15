<?php

/**
 * $Id$
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_renderer.class.php');
require_once (Path :: get_application_library_path().'monthcalendar.class.php');
/**
 * This personal calendar renderer provides a tabular month view of the events
 * in the calendar.
 */
class PersonalCalendarMonthRenderer extends PersonalCalendarRenderer
{
	/**
	 * @see PersonalCalendarRenderer::render()
	 */
	public function render()
	{
		$calendar = new MonthCalendar($this->get_time());
		$from_date = strtotime(date('Y-m-1', $this->get_time()));
		$to_date = strtotime('-1 Second', strtotime('Next Month', $from_date));
		$events = $this->get_events($from_date, $to_date);

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
		$html[] = date('H:i', $event->get_start_date()).' '.htmlspecialchars($event->get_title());
		$html[] = '</a>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
}
?>