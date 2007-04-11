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
	/**
	 * @see PersonalCalendarRenderer::render()
	 */
	public function render()
	{
		// Range from start (0) to 10 years in the future...
		$events = $this->get_events(0, strtotime('+10 Years', time()));
		$dm = RepositoryDataManager :: get_instance();
		$html = array ();
		foreach ($events as $index => $event)
		{
			switch (get_class($event))
			{
				case 'PersonalCalendarEvent' :
					$learning_object = $event->get_event();
					$display = LearningObjectDisplay :: factory($learning_object);
					$html[$learning_object->get_start_date()][] = $display->get_full_html();
					break;
				case 'LearningObjectPublicationAttributes' :
					$learning_object = $dm->retrieve_learning_object($event->get_publication_object_id());
					$display = LearningObjectDisplay :: factory($learning_object);
					$html[$learning_object->get_start_date()][] = $display->get_full_html();
					break;
			}
		}
		ksort($html);
		$out = '';
		foreach($html as $time => $content)
		{
			$out .= implode("\n", $content);
		}
		return $out;
	}
}
?>