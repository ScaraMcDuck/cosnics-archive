<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_renderer.class.php');
require_once (Path :: get_application_library_path().'minimonthcalendar.class.php');
/**
 * This personal calendar renderer provides a tabular month view to navigate in
 * the calendar
 */
class PersonalCalendarMiniMonthRenderer extends PersonalCalendarRenderer
{
	/**
	 * @see PersonalCalendarRenderer::render()
	 */
	public function render()
	{
		$calendar = new MiniMonthCalendar($this->get_time());
		$from_date = strtotime(date('Y-m-1',$this->get_time()));
		$to_date = strtotime('-1 Second',strtotime('Next Month',$from_date));
		$events = $this->get_events($from_date,$to_date);
		$dm = RepositoryDataManager::get_instance();
		$html = array();
		foreach($events as $index => $event)
		{
			$content = $this->render_event($event);
			$calendar->add_event($event->get_start_date(),$content);
		}
		$parameters['time'] = '-TIME-';
		$calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
		switch($this->get_parent()->get_parameter('view'))
		{
			case 'month':
				$calendar->mark_period(MiniMonthCalendar::PERIOD_MONTH);
				break;
			case 'week':
				$calendar->mark_period(MiniMonthCalendar::PERIOD_WEEK);
				break;
			case 'day':
				$calendar->mark_period(MiniMonthCalendar::PERIOD_DAY);
				break;
		}
		$calendar->add_navigation_links($this->get_parent()->get_url($parameters));
		$html = $calendar->toHtml();
		return $html;
	}
	/**
	 * Gets a html representation of a published calendar event
	 * @param PersonalCalendarEvent $event
	 * @return string
	 */
	private function render_event($event)
	{
		$html[] = '<br /><img src="'.Theme :: get_common_img_path().'posticon.png"/>';
		return implode("\n",$html);
	}
}
?>