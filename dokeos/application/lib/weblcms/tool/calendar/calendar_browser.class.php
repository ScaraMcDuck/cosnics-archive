<?php
/**
 * $Id$
 * Calendar tool - browser
 * @package application.weblcms.tool
 * @subpackage calendar
 */
require_once dirname(__FILE__).'/../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/calendar_list_renderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/mini_month_calendar_learning_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/month_calendar_learning_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/week_calendar_learning_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/day_calendar_learning_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/learning_object_publication_details_renderer.class.php';

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
		if(isset($_GET['pid']))
		{
			$this->set_publication_id($_GET['pid']);
			$renderer = new LearningObjectPublicationDetailsRenderer($this);
		}
		else
		{
			$renderer = new CalendarListRenderer($this);
		}
		$this->set_publication_list_renderer($renderer);
	}

	function set_view($view = CALENDAR_MONTH_VIEW,$time = null)
	{
		if(is_null($time))
		{
			$time = time();
		}
		$this->time = $time;
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
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$groups = $this->get_groups();
		}
		$datamanager = WeblcmsDataManager :: get_instance();
		$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'calendar');
		$this->publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $groups,$condition)->as_array();
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
	public function as_html()
	{
		$minimonthcalendar = new MiniMonthCalendarLearningObjectPublicationListRenderer($this);
		$minimonthcalendar->set_display_time($this->time);
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] =  $minimonthcalendar->as_html();
		$html[] =  '</div>';
		$html[] =  '<div style="float: left; width: 80%;">';
		$html[] = parent::as_html();
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>