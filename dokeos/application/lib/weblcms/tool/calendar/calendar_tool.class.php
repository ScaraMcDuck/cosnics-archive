<?php
/**
 * $Id$
 * Calendar tool
 * @package application.weblcms.tool
 * @subpackage calendar
 */
require_once Path :: get_repository_path(). 'lib/learning_object/calendar_event/calendar_event.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/../repository_tool.class.php';
require_once dirname(__FILE__).'/../../learning_object_publisher.class.php';
require_once dirname(__FILE__).'/calendar_browser.class.php';
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
		$trail = new BreadcrumbTrail();
		
		if (isset($_GET['admin']))
		{
			$_SESSION['calendaradmin'] = $_GET['admin'];
		}
		if ($_SESSION['calendaradmin'])
		{
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.Theme :: get_common_img_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			require_once dirname(__FILE__).'/../../learning_object_publisher.class.php';
			$pub = new LearningObjectPublisher($this, 'calendar_event');
			$event = new CalendarEvent();
			$event->set_owner_id($this->get_user_id());
			$event->set_start_date(intval($_GET['default_start_date']));
			$event->set_end_date(intval($_GET['default_end_date']));
			$pub->set_default_learning_object('calendar_event',$event);
			$html[]= $pub->as_html();
			$this->display_header($trail);
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$this->display_header($trail);
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p><a href="' . $this->get_url(array('admin' => 1), true) . '"><img src="'.Theme :: get_common_img_path().'action_publish.png" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a></p>';
			}
			echo $this->perform_requested_actions();
			if($_GET[self :: PARAM_ACTION] == self :: ACTION_DELETE || $_GET['view'] == 'list')
			{
				unset($_GET[self :: PARAM_PUBLICATION_ID]);
			}
			$this->display();
			$this->display_footer();
		}
	}
	/**
	 * Display the calendar
	 */
	function display()
	{
		$time = isset($_GET['time']) ? intval($_GET['time']) : time();
		$this->set_parameter('time',$time);
		$toolbar_data = array();
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'list')),
			'img' => Theme :: get_img_path().'tool_calendar_down.png',
			'label' => Translation :: get('ListView'),
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'month')),
			'img' => Theme :: get_img_path().'tool_calendar_month.png',
			'label' => Translation :: get('MonthView'),
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'week')),
			'img' => Theme :: get_img_path().'calendar_week.png',
			'label' => Translation :: get('WeekView'),
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'day')),
			'img' => Theme :: get_img_path().'tool_calendar_day.png',
			'label' => Translation :: get('DayView'),
			'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		echo '<div style="margin-bottom: 1em;">'.DokeosUtilities :: build_toolbar($toolbar_data).'</div>';
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
		$datamanager = WeblcmsDataManager :: get_instance();
		$publication = $datamanager->retrieve_learning_object_publication($publication_id);
		$html = array();
		$html[] = $renderer->render_publication($publication);
		$html[] = '<div id="back_link" style="margin-top: 1em;"><a href="'.$this->get_url(array(), true).'"><img src="'.Theme :: get_common_img_path().'action_prev.png"/> '.htmlentities(Translation :: get('Back')).'</a></div>';
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
		$datamanager = WeblcmsDataManager :: get_instance();
		$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'calendar');
		$this->publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition)->as_array();
		return $this->publications;
	}
}
?>