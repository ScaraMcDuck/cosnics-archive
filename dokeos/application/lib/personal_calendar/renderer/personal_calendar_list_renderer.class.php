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
			$html[$event->get_start_date()][] = $this->render_event($event);
		}
		ksort($html);
		$out = '';
		foreach($html as $time => $content)
		{
			$out .= implode("\n", $content);
		}
		return $out;
	}
	
	function render_event($event)
	{
		$html = array();
		$date_format = Translation :: get('dateTimeFormatLong');
		
		$html[] = '<div class="learning_object" style="background-image: url(' . Theme :: get_common_image_path() . 'learning_object/calendar_event.png);">';
		$html[] = '<div class="title">'. htmlentities($event->get_title()) .'</div>';
		$html[] = '<div class="description">';
		if ($event->get_end_date() != '')
		{
			$html[] = '<div class="calendar_event_range">'.htmlentities(Translation :: get('From').' '.Text :: format_locale_date($date_format, $event->get_start_date()).' '.Translation :: get('Until').' '.Text :: format_locale_date($date_format, $event->get_end_date())).'</div>';
		}
		else
		{
			$html[] = '<div class="calendar_event_range">'.Text :: format_locale_date($date_format, $event->get_start_date()).'</div>';
		}
		$html[] = $event->get_content();
		$html[] = $this->render_attachments($event);
		$html[] = '</div></div>';
		
		return implode("\n", $html);
	}
	
	function render_attachments($event)
	{ 
		if(is_null($event->get_id())) return;
		
		$publication = PersonalCalendarDataManager :: get_instance()->retrieve_calendar_event_publication($event->get_id());
		$object = $publication->get_publication_object();
		if ($object->supports_attachments())
		{
			$attachments = $object->get_attached_learning_objects();
			if(count($attachments)>0)
			{
				$html[] = '<h4>Attachments</h4>';
				DokeosUtilities :: order_learning_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$disp = LearningObjectDisplay :: factory($attachment);
					$html[] = '<div class="learning_object" style="background-image: url(' . Theme :: get_common_image_path().'learning_object/'.$attachment->get_icon_name().'.png);">';
					$html[] = '<div class="title">';
					$html[] = $attachment->get_title();
					$html[] = '</div>';
					$html[] = '<div class="description">';
					$html[] = $attachment->get_description();
					$html[] = '</div></div>';
				}
				//$html[] = '</ul>';
				return implode("\n",$html);
			}
		}
		return '';
	}
}
?>