<?php
require_once (dirname(__FILE__).'/../personal_calendar_renderer.class.php');
class PersonalCalendarListRenderer extends PersonalCalendarRenderer
{
	public function render()
	{
		$events = $this->get_events(time(),time());
		$dm = RepositoryDataManager::get_instance();
		foreach($events as $index => $event)
		{
			$learning_object = $dm->retrieve_learning_object($event->get_publication_object_id());
			$display = LearningObjectDisplay :: factory($learning_object);
			echo $display->get_full_html();
		}
	}
}
?>