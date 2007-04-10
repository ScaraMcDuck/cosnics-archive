<?php
/**
 * $Id: personal_calendar_list_renderer.class.php 11660 2007-03-22 14:17:03Z bmol $
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_renderer.class.php');
require_once (dirname(__FILE__).'/../../../common/monthcalendar.class.php');
/**
 * This personal calendar renderer provides a tabular month view of the events
 * in the calendar.
 */
class PersonalCalendarMonthRenderer extends PersonalCalendarRenderer
{
	public function render()
	{
		$calendar = new MonthCalendar($this->get_time());
		$from_date = strtotime(date('Y-m-1',$this->get_time()));
		$to_date = strtotime('-1 Second',strtotime('Next Month',$from_date));
		$events = $this->get_events($from_date,$to_date);

		$html = array();
		foreach($events as $index => $event)
		{
			$dm = RepositoryDataManager::get_instance();
			$lo = $dm->retrieve_learning_object($event->get_publication_object_id());
			$content = $this->render_event($event);
			$calendar->add_event($lo->get_start_date(),$content);
		}
		$parameters['time'] = '-TIME-';
		$calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
		return $calendar->toHtml();
	}
	private function render_event($publication)
	{
		$dm = RepositoryDataManager::get_instance();
		$event = $dm->retrieve_learning_object($publication->get_publication_object_id());
		$start_date = $event->get_start_date();
		$end_date = $event->get_end_date();
		$html[] = '<div class="event">';
		$html[] = '<a href="'.$publication->get_url().'">';
		$html[] = date('H:i',$start_date).' '.htmlspecialchars($event->get_title());
		$html[] = '</a>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>