<?php
/**
 * Calendar tool
 * @package application.weblcms.tool
 * @subpackage calendar
 */
require_once dirname(__FILE__).'/../../../../../repository/lib/learning_object/calendar_event/calendar_event.class.php';
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
require_once dirname(__FILE__).'/calendarbrowser.class.php';
/**
 * This tool allows a user to publish events in his or her course.
 * There are 4 calendar views available:
 * - list view (chronological list of events)
 * - month view
 * - week view
 * - day view
 */
class CalendarTool extends RepositoryTool
{
	/**
	 * Inherited.
	 */
	function run()
	{
		if (isset($_GET['calendaradmin']))
		{
			$_SESSION['calendaradmin'] = $_GET['calendaradmin'];
		}
		if ($_SESSION['calendaradmin'])
		{
			echo '<p>Go to <a href="' . $this->get_url(array('calendaradmin' => 0), true) . '">User Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'calendar_event');
			$event = new CalendarEvent();
			$event->set_start_date(intval($_GET['default_start_date']));
			$event->set_end_date(intval($_GET['default_end_date']));
			$pub->set_default_learning_object('calendar_event',$event);
			echo $pub->as_html();
		}
		else
		{
			echo '<p>Go to <a href="' . $this->get_url(array('calendaradmin' => 1), true) . '">Publisher Mode</a> &hellip;</p>';
			$this->perform_requested_actions();
			if($_GET[self :: PARAM_ACTION] == self :: ACTION_DELETE || $_GET['view'] == 'list')
			{
				unset($_GET[self :: PARAM_PUBLICATION_ID]);
			}
			$this->display();
		}
	}
	/**
	 * Display the calendar
	 */
	function display()
	{
		$time = isset($_GET['time']) ? intval($_GET['time']) : time();
		$this->set_parameter('time',$time);
		echo '<a href="'.$this->get_url(array('view'=>'list'), true).'">list</a> | ';
		echo '<a href="'.$this->get_url(array('view'=>'month'), true).'">month</a> | ';
		echo '<a href="'.$this->get_url(array('view'=>'week'), true).'">week</a> | ';
		echo '<a href="'.$this->get_url(array('view'=>'day'), true).'">day</a> <br/><br/>';
		$show_calendar = true;
		if(isset($_GET['pid']))
		{
			$this->set_parameter('view',$_GET['view']);
			$this->display_publication($_GET['pid']);
			$show_calendar = false;
		}
		if($show_calendar)
		{
			$browser = new CalendarBrowser($this);
			switch($_GET['view'])
			{
				case 'list':
					$this->set_parameter('view','list');
					$browser->set_view(CalendarBrowser::CALENDAR_LIST_VIEW,$time);
					break;
				case 'day':
					$this->set_parameter('view','day');
					$browser->set_view(CalendarBrowser::CALENDAR_DAY_VIEW,$time);
					break;
				case 'week':
					$this->set_parameter('view','week');
					$browser->set_view(CalendarBrowser::CALENDAR_WEEK_VIEW,$time);
					break;
				default:
					$this->set_parameter('view','month');
					$browser->set_view(CalendarBrowser::CALENDAR_MONTH_VIEW,$time);
					break;
			}
			echo $browser->as_html();
		}
	}
	/**
	 * Display a pubication
	 */
	function display_publication($publication_id)
	{
		$browser = new CalendarBrowser($this);
		$renderer = $browser->get_publication_list_renderer();
		$datamanager = WebLCMSDataManager :: get_instance();
		$publication = $datamanager->retrieve_learning_object_publication($publication_id);
		$html = array();
		$html[] = '<a href="'.$this->get_url(array(), true).'">&laquo;&laquo; '.get_lang('Back').'</a>';
		$html[] = $renderer->render_publication($publication);
		echo implode("\n",$html);
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
	/**
	 * Get the list of published announcements
	 * @return array An array with all publications of announcements
	 */
	function get_publications()
	{
		if( isset($this->publications))
		{
			return $this->publications;
		}
		$datamanager = WebLCMSDataManager :: get_instance();
		$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'calendar');
		$this->publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition);
		return $this->publications;
	}
}
?>