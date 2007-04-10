<?php
/**
 * $Id: personal_calendar_list_renderer.class.php 11660 2007-03-22 14:17:03Z bmol $
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_renderer.class.php');
require_once (dirname(__FILE__).'/../../../common/minimonthcalendar.class.php');
/**
 * This personal calendar renderer provides a tabular month view to navigate in
 * the calendar
 */
class PersonalCalendarMiniMonthRenderer extends PersonalCalendarRenderer
{
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
			$learning_object = $dm->retrieve_learning_object($event->get_publication_object_id());
			$content = $this->render_event($learning_object);
			$calendar->add_event($learning_object->get_start_date(),$content);
		}
		$parameters['time'] = '-TIME-';
		$calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
		switch($this->get_parent()->get_parameter('view'))
		{
			case 'week':
				$calendar->mark_period(MiniMonthCalendar::PERIOD_WEEK);
				break;
			case 'day':
				$calendar->mark_period(MiniMonthCalendar::PERIOD_DAY);
				break;
		}
		$calendar->add_navigation_links($this->get_parent()->get_url($parameters));
		$html = $calendar->toHtml();
		$html = str_replace('class="calendar_navigation"','class="calendar_navigation mini"',$html);
		return $html;
	}
	private function render_event($event)
	{
		$start_date = $event->get_start_date();
		$end_date = $event->get_end_date();
		$html[] = '<br /><img src="'.api_get_path(WEB_CODE_PATH).'/img/posticon.gif"/>';
		return implode("\n",$html);
	}
}
?>