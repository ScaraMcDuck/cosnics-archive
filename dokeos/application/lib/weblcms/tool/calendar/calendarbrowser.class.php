<?php
/**
 * Calendar tool - browser
 * @package application.weblcms.tool
 * @subpackage calendar
 */
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once dirname(__FILE__).'/calendarlistrenderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/monthcalendarlearningobjectpublicationlistrenderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/weekcalendarlearningobjectpublicationlistrenderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/daycalendarlearningobjectpublicationlistrenderer.class.php';

class CalendarBrowser extends LearningObjectPublicationBrowser
{
	const CALENDAR_MONTH_VIEW = 'month';
	const CALENDAR_WEEK_VIEW = 'week';
	const CALENDAR_DAY_VIEW = 'day';
	const CALENDAR_LIST_VIEW = 'list';
	private $publications;
	function CalendarBrowser($parent, $types)
	{
		parent :: __construct($parent, 'calendar');
		$renderer = new CalendarListRenderer($this);
		$this->set_publication_list_renderer($renderer);
	}

	function set_view($view = CALENDAR_MONTH_VIEW,$time = null)
	{
		if(is_null($time))
		{
			$time = time();
		}
		switch($view)
		{
			case CalendarBrowser::CALENDAR_DAY_VIEW:
			{
				$renderer = new DayCalendarLearningObjectPublicationListRenderer($this);
				$renderer->set_display_time($time);
				break;
			}
			case CalendarBrowser::CALENDAR_WEEK_VIEW:
			{
				$renderer = new WeekCalendarLearningObjectPublicationListRenderer($this);
				$renderer->set_display_time($time);
				break;
			}
			case CalendarBrowser::CALENDAR_MONTH_VIEW:
			{
				$renderer = new MonthCalendarLearningObjectPublicationListRenderer($this);
				$renderer->set_display_time($time);
				break;
			}
			default:
			{
				$renderer = new CalendarListRenderer($this);
				break;
			}
		}
		$this->set_publication_list_renderer($renderer);
	}


	function get_publications($from, $count, $column, $direction)
	{
		if( isset($this->publications))
		{
			return $this->publications;
		}
		$datamanager = WeblcmsDataManager :: get_instance();
		$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'calendar');
		$this->publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition);
		return $this->publications;
	}

	function get_publication_count()
	{
		return count($this->get_publications());
	}

	/**
	 * Get calendar events in a certain time range
	 * @param int $from_time
	 * @param int $to_time
	 * @return array A set of publications of calendar_events
	 */
	function get_calendar_events($from_time,$to_time)
	{
		$publications = $this->get_publications();
		$events = array();
		foreach($publications as $index => $publication)
		{
			$event = $publication->get_learning_object();
			$start_date = $event->get_start_date();
			$end_date = $event->get_end_date();
			if($from_time <= $start_date && $start_date <= $to_time || $from_time <= $end_date && $end_date <= $to_time || $start_date <= $from_time && $to_time <= $end_date)
			{
				$events[] = $publication;
			}
		}
		return $events;
	}
}
?>