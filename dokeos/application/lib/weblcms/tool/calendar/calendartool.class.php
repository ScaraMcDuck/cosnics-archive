<?php
/**
 * Calendar tool
 * @package application.weblcms.tool
 * @subpackage calendar
 */
require_once dirname(__FILE__).'/../../../../../repository/lib/learning_object/calendar_event/calendar_event.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/repositoryutilities.class.php';
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
		if (isset($_GET['admin']))
		{
			$_SESSION['calendaradmin'] = $_GET['admin'];
		}
		if ($_SESSION['calendaradmin'])
		{
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.api_get_path(WEB_CODE_PATH).'/img/browser.gif" alt="'.get_lang('BrowserTitle').'" style="vertical-align:middle;"/> '.get_lang('BrowserTitle').'</a></p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'calendar_event');
			$event = new CalendarEvent();
			$event->set_owner_id($this->get_user_id());
			$event->set_start_date(intval($_GET['default_start_date']));
			$event->set_end_date(intval($_GET['default_end_date']));
			$pub->set_default_learning_object('calendar_event',$event);
			$html[]= $pub->as_html();
			$this->display_header();
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$this->display_header();
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p><a href="' . $this->get_url(array('admin' => 1), true) . '"><img src="'.api_get_path(WEB_CODE_PATH).'/img/publish.gif" alt="'.get_lang('Publish').'" style="vertical-align:middle;"/> '.get_lang('Publish').'</a></p>';
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
			'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_down.gif',
			'label' => get_lang('ListView'),
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'month')),
			'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_month.gif',
			'label' => get_lang('MonthView'),
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'week')),
			'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_week.gif',
			'label' => get_lang('WeekView'),
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'day')),
			'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_day.gif',
			'label' => get_lang('DayView'),
			'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
		);
		echo '<div style="margin-bottom: 1em;">'.RepositoryUtilities :: build_toolbar($toolbar_data).'</div>';
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
		$html[] = '<div id="back_link" style="margin-top: 1em;"><a href="'.$this->get_url(array(), true).'">&larr; '.htmlentities(get_lang('Back')).'</a></div>';
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