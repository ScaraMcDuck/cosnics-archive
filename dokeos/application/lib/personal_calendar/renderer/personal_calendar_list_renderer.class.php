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
		
		$html[] = '<div class="learning_object" style="background-image: url(' . Theme :: get_common_image_path() . $event->get_source().'.png);">';
		$html[] = '<div class="title">'. htmlentities($event->get_title()) .'</div>';
		
		if ($event->get_end_date() != '')
		{
			$html[] = '<div class="calendar_event_range">'.htmlentities(Translation :: get('From').' '.Text :: format_locale_date($date_format, $event->get_start_date()).' '.Translation :: get('Until').' '.Text :: format_locale_date($date_format, $event->get_end_date())).'</div>';
		}
		else
		{
			$html[] = '<div class="calendar_event_range">'.Text :: format_locale_date($date_format, $event->get_start_date()).'</div>';
		}
		$html[] = $event->get_content();
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}
?>