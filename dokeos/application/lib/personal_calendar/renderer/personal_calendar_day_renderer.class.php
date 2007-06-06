<?php
/**
 * $Id$
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
					break;
			}
		}
		$parameters['time'] = '-TIME-';
		$calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
		$html = $calendar->toHtml();
		$html .= $this->build_legend();
		return $html;
	}
	/**
	 * Renders a personal event
	 * @param PersonalCalendarEvent $personal_event
	 * @return string
	 */
	private function render_personal_event($personal_event)
	{
		$learning_object = $personal_event->get_event();
		$start_date = $learning_object->get_start_date();
		$end_date = $learning_object->get_end_date();
		$html[] = '<div class="event" style="border-left: 5px solid '.$this->get_color().';">';
		$html[] = '<a href="'.$this->get_url(array('pid'=>$personal_event->get_id())).'">';
		$html[] = date('H:i', $start_date).' '.htmlspecialchars($learning_object->get_title());
		$html[] = '</a>';
		$html[] = '<a href="" style="position:absolute;right: 15px;"><img src="'.api_get_path(WEB_CODE_PATH).'/img/delete.gif"/></a>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	/**
	 * Renders an event.
	 * This function is used to render an event which is retrieved from an other
	 * application using a personal calendar connector.
	 * @param LearningObjectPublicationAttributes $event
	 * @return string
	 */
	private function render_event($event)
	{
		$dm = RepositoryDataManager :: get_instance();
		$learning_object = $dm->retrieve_learning_object($event->get_publication_object_id());
		$start_date = $learning_object->get_start_date();
		$end_date = $learning_object->get_end_date();
		$html[] = '<div class="event" style="border-left: 5px solid '.$this->get_color($event->get_location()).';">';
		$html[] = '<a href="'.$event->get_url().'">';
		$html[] = date('H:i', $start_date).' '.htmlspecialchars($learning_object->get_title());
		$html[] = '</a>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
}
?>