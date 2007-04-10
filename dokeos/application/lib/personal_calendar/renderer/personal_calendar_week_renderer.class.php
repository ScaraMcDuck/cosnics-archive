<?php
/**
 * $Id: personal_calendar_list_renderer.class.php 11660 2007-03-22 14:17:03Z bmol $
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_renderer.class.php');
require_once (dirname(__FILE__).'/../../../common/weekcalendar.class.php');
/**
 * This personal calendar renderer provides a tabular week view of the events in
 * the calendar.
 */
class PersonalCalendarWeekRenderer extends PersonalCalendarRenderer
{
	public function render()
	{
		$calendar = new WeekCalendar($this->get_time());
		$from_date = strtotime('Last Monday',strtotime(date('Y-m-d',$this->get_time())));
		$to_date = strtotime('-1 Second',strtotime('Next Week',$from_date));
		$events = $this->get_events($from_date,$to_date);
		$dm = RepositoryDataManager::get_instance();
		$html = array();
		foreach($events as $index => $event)
		{
			$learning_object = $dm->retrieve_learning_object($event->get_publication_object_id());
			$content = $this->render_event($event);
			$calendar->add_event($learning_object->get_start_date(),$content);
		}
		$parameters['time'] = '-TIME-';
		$calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
		return $calendar->toHtml();
	}
	private function render_event($event)
	{
		$dm = RepositoryDataManager::get_instance();
		$learning_object = $dm->retrieve_learning_object($event->get_publication_object_id());
		$start_date = $learning_object->get_start_date();
		$end_date = $learning_object->get_end_date();
		$html[] = '<div class="event">';
		$html[] = '<a href="'.$event->get_url().'">';
		$html[] = date('H:i',$start_date).' '.htmlspecialchars($learning_object->get_title());
		$html[] = '</a>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>