<?php
/**
 * $Id$
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
	/**
	 * @see PersonalCalendarRenderer::render()
	 */
	public function render()
	{
		$calendar = new WeekCalendar($this->get_time());
		$from_date = strtotime('Last Monday', strtotime(date('Y-m-d', $this->get_time())));
		$to_date = strtotime('-1 Second', strtotime('Next Week', $from_date));
		$events = $this->get_events($from_date, $to_date);
		$dm = RepositoryDataManager :: get_instance();
		$html = array ();
		foreach ($events as $index => $event)
		{
			switch (get_class($event))
			{
				case 'PersonalCalendarEvent' :
					$content = $this->render_personal_event($event);
					$calendar->add_event($event->get_event()->get_start_date(), $content);
					break;
				case 'LearningObjectPublicationAttributes' :
					$learning_object = $dm->retrieve_learning_object($event->get_publication_object_id());
					$content = $this->render_event($event);
					$calendar->add_event($learning_object->get_start_date(), $content);
			}
		}
		$parameters['time'] = '-TIME-';
		$calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
		return $calendar->toHtml();
	}
	/**
	 * Gets a html representation of a personal calendar event
	 * @param PersonalCalendarEvent $personal_event
	 * @return string
	 */
	private function render_personal_event($personal_event)
	{
		$learning_object = $personal_event->get_event();
		$start_date = $learning_object->get_start_date();
		$end_date = $learning_object->get_end_date();
		$html[] = '<div class="event">';
		$html[] = '<a href="'.$this->get_url(array('pid'=>$personal_event->get_id())).'">';
		$html[] = date('H:i', $start_date).' '.htmlspecialchars($learning_object->get_title());
		$html[] = '</a>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	/**
	 * Gets a html representation of a published calendar event
	 * @param LearningObjectPublicationAttributes $event
	 * @return string
	 */
	private function render_event($event)
	{
		$dm = RepositoryDataManager :: get_instance();
		$learning_object = $dm->retrieve_learning_object($event->get_publication_object_id());
		$start_date = $learning_object->get_start_date();
		$end_date = $learning_object->get_end_date();
		$html[] = '<div class="event">';
		$html[] = '<a href="'.$event->get_url().'">';
		$html[] = date('H:i', $start_date).' '.htmlspecialchars($learning_object->get_title());
		$html[] = '</a>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
}
?>