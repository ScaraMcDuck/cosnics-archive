<?php
/**
 * $Id: personal_calendar_list_renderer.class.php 11660 2007-03-22 14:17:03Z bmol $
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_renderer.class.php');
require_once (dirname(__FILE__).'/../../../common/daycalendar.class.php');
/**
 * This personal calendar renderer provides a tabular day view of the events in
 * the calendar.
 */
class PersonalCalendarDayRenderer extends PersonalCalendarRenderer
{
	public function render()
	{
		$calendar = new DayCalendar($this->get_time());
		$events = $this->get_events($this->get_time(),$this->get_time());
		$dm = RepositoryDataManager::get_instance();
		$html = array();
		foreach($events as $index => $event)
		{
			$learning_object = $dm->retrieve_learning_object($event->get_publication_object_id());
			$content = $this->render_event($learning_object);
			$calendar->add_event($learning_object->get_start_date(),$content);
		}
		return $calendar->toHtml();
	}
	private function render_event($event)
	{
		$start_date = $event->get_start_date();
		$end_date = $event->get_end_date();
		$html[] = '<div class="event">';
		$html[] = date('H:i',$start_date).' '.htmlspecialchars($event->get_title());
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>