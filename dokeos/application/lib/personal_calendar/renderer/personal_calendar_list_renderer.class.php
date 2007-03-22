<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_renderer.class.php');
/**
 * This personal calendar renderer provides a simple list view of the events in
 * the calendar.
 */
class PersonalCalendarListRenderer extends PersonalCalendarRenderer
{
	public function render()
	{
		$events = $this->get_events(time(),time());
		$dm = RepositoryDataManager::get_instance();
		$html = array();
		foreach($events as $index => $event)
		{
			$learning_object = $dm->retrieve_learning_object($event->get_publication_object_id());
			$display = LearningObjectDisplay :: factory($learning_object);
			$html[] = $display->get_full_html();
		}
		return implode("\n",$html);
	}
}
?>