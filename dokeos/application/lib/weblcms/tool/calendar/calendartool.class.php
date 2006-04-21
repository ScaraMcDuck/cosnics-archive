<?php
/**
 * Calendar tool
 * @package application.weblcms.tool
 * @subpackage calendar
 */
require_once dirname(__FILE__).'/../../../../../repository/lib/learning_object/calendar_event/calendar_event.class.php';
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
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
			switch($_GET['view'])
			{
				case 'list':
					$this->set_parameter('view','list');
					$this->display_list_view($time);
					break;
				case 'day':
					$this->set_parameter('view','day');
					$this->display_day_view($time);
					break;
				case 'week':
					$this->set_parameter('view','week');
					$this->display_week_view($time);
					break;
				default:
					$this->set_parameter('view','month');
					$this->display_month_view($time);
					break;
			}
		}
	}
	/**
	 * Display a month view of the calendar
	 * @param int $time A moment in the month to be displayed
	 */
	function display_month_view($time)
	{
		$calendar_table = new HTML_Table(array ('class' => 'calendar'));
		$m = date('m',$time);
		$y = date('Y',$time);
		$first_day = mktime(0, 0, 0, $m, 1, $y);
		$first_day_nr = date('w', $first_day) == 0 ? 6 : date('w', $first_day) - 1;
		$calendar_table->addRow(array ('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'));
		$first_table_date = strtotime('Monday',strtotime('-1 Week',$first_day));
		$table_date = $first_table_date;
		$cell = 0;
		while(date('Ym',$table_date) <= date('Ym',$time))
		{
			do
			{
				$params = array('default_start_date' => $table_date,'default_end_date' => $table_date, LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator','calendaradmin' => '1');
				$add_url = $this->get_url($params);
				$cell_contents = '<a href="'.$add_url.'">'.date('d',$table_date).'</a>';
				$publications = $this->get_calendar_events($table_date,strtotime('+1 Day',$table_date));
				foreach($publications as $index => $publication)
				{
					$event = $publication->get_learning_object();
					$event_url = $this->get_url(array('pid'=>$publication->get_id()), true);
					$cell_contents .= '<div class="event"><a href="'.$event_url.'">'.date('H:i',$event->get_start_date()).' '.htmlentities($event->get_title()).'</a></div>';
				}
				$calendar_table->setCellContents(intval($cell / 7) + 1, $cell % 7, $cell_contents );
				if(date('Ymd',$table_date) == date('Ymd'))
				{
					$calendar_table->updateCellAttributes(intval($cell / 7) + 1, $cell % 7,'class="highlight"');
				}
				$cell++;
				$table_date = strtotime('+1 Day',$table_date);
			}
			while($cell%7 != 0);
		}
		$calendar_table->setRowType(0,'th');
		$prev = strtotime('-1 Month',$time);
		$next = strtotime('+1 Month',$time);
		echo '<div style="text-align: center;">';
		echo '<a href="'.$this->get_url(array('time' => $prev), true).'">&lt;&lt;</a> ';
		echo date('F Y',$first_day);
		echo ' <a href="'.$this->get_url(array('time' => $next), true).'">&gt;&gt;</a> ';
		echo '</div>';
		$calendar_table->display();
	}
	/**
	 * Display a week view of the calendar
	 * @param int $time A moment in the week to be displayed
	 */
	function display_week_view($time)
	{
		$week_number = date('W',$time);
		// Go 1 week back end them jump to the next monday to reach the first day of this week
		$first_day = strtotime('Monday',strtotime('-1 Week',$time));
		$last_day = strtotime('Sunday',$first_day);
		$calendar_table = new HTML_Table(array ('class' => 'calendar'));
		for($hour = 0; $hour < 24; $hour += 4)
		{
			$cell_content = $hour.'u - '.($hour+4).'u';
			$calendar_table->setCellContents($hour/4+1,0,$cell_content);
			for($column = 1; $column <= 7; $column++)
			{
				$day = strtotime('+'.($column-1).' day',$first_day);
				$default_start_date = mktime($hour,0,0,date('m',$day),date('d',$day),date('Y',$day));
				$params = array('default_start_date' => $default_start_date,'default_end_date' => strtotime('+'.(date('H',$default_start_date)+4).' hours',$default_start_date),'publish_action' => 'publicationcreator','calendaradmin' => '1');
				$add_url = $this->get_url($params);
				$calendar_table->setCellContents($hour/4+1,$column,'<div style="text-align:right;"><a href="'.$add_url.'">+</a></div>');
			}
		}
		$dates[] = '';
		$today = date('Y-m-d');
		for($day = 0; $day < 7; $day++)
		{
			$week_day = strtotime('+'.$day.' days',$first_day);
			$calendar_table->setCellContents(0,$day+1,date('l',$week_day).'<br/>'.date('Y-m-d',$week_day));
			if($today == date('Y-m-d',$week_day))
			{
				$calendar_table->updateColAttributes($day+1,'class="highlight"');
			}
		}
		$from_time = mktime(0,0,0,date('m',$first_day),date('d',$first_day),date('Y',$first_day));
		$to_time = mktime(23,59,59,date('m',$last_day),date('d',$last_day),date('Y',$last_day));
		$publications = $this->get_calendar_events($from_time,$to_time);
		foreach($publications as $index => $publication)
		{
			$event = $publication->get_learning_object();
			$row = date('H',$event->get_start_date())/4+1;
			$col = date('w',$event->get_start_date());
			$col = ($col == 0 ? 7 : $col);
			$event_url = $this->get_url(array('pid'=>$publication->get_id()), true);
			$cell_contents = $calendar_table->getCellContents($row,$col);
			$cell_contents .= '<div class="event"><a href="'.$event_url.'">'.date('H:i',$event->get_start_date()).' '.htmlentities($event->get_title()).'</a></div>';
			$calendar_table->setCellContents($row,$col,$cell_contents);
		}
		$calendar_table->setRowType(0,'th');
		$calendar_table->setColType(0,'th');
		$prev = strtotime('-1 Week',$time);
		$next = strtotime('+1 Week',$time);
		echo '<div style="text-align: center;">';
		echo '<a href="'.$this->get_url(array('time' => $prev), true).'">&lt;&lt;</a> ';
		echo get_lang('Week').' '.$week_number.' : '.date('l d M Y',$first_day).' - '.date('l d M Y',strtotime('+6 Days',$first_day));
		echo ' <a href="'.$this->get_url(array('time' => $next), true).'">&gt;&gt;</a> ';
		echo '</div>';
		$calendar_table->display();
	}
	/**
	 * Display a day view of the calendar
	 * @param int $time A moment in the day to be displayed
	 */
	function display_day_view($time)
	{
		$calendar_table = new HTML_Table(array ('class' => 'calendar'));
		for($hour = 0; $hour < 24; $hour += 2)
		{
			$default_start_date = mktime($hour,0,0,date('m',$time),date('d',$time),date('Y',$time));
			$params = array('default_start_date' => $default_start_date,'default_end_date' => strtotime('+'.(date('H',$default_start_date)+2).' hours',$default_start_date),'publish_action' => 'publicationcreator','calendaradmin' => '1');
			$add_url = $this->get_url($params);
			$cell_contents = '<a href="'.$add_url.'">'.$hour.'u - '.($hour+2).'u'.'</a>';
			$calendar_table->setCellContents($hour/2,0,$cell_contents);

		}
		$from_time = mktime(0,0,0,date('m',$time),date('d',$time),date('Y',$time));
		$to_time = mktime(23,59,59,date('m',$time),date('d',$time),date('Y',$time));
		$publications = $this->get_calendar_events($from_time,$to_time);
		foreach($publications as $index => $publication)
		{
			$event = $publication->get_learning_object();
			$event_url = $this->get_url(array('pid'=>$publication->get_id()), true);
			$row = 	date('H',$event->get_start_date())/2;
			$cell_contents = $calendar_table->getCellContents($row,0);
			$cell_contents .= '<div class="event"><a href="'.$event_url.'">'.date('H:i',$event->get_start_date()).' '.htmlentities($event->get_title()).'</a><br/>'.$event->get_description().'</div>';
			$calendar_table->setCellContents($row,0,$cell_contents);
		}
		$prev = strtotime('-1 Day',$time);
		$next = strtotime('+1 Day',$time);
		echo '<div style="text-align: center;">';
		echo '<a href="'.$this->get_url(array('time' => $prev), true).'">&lt;&lt;</a> ';
		echo date('l d F Y',$time);
		echo ' <a href="'.$this->get_url(array('time' => $next), true).'">&gt;&gt;</a> ';
		echo '</div>';
		$calendar_table->display();
	}
	/**
	 * Display a day view of the calendar
	 * @param int $time A moment in the day to be displayed
	 */
	function display_list_view($time)
	{
		$all_publications = $this->get_publications();
		$renderer = new CalendarBrowser($this);
		$visible_publications = array();
		foreach($all_publications as $index => $publication)
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if(!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$visible_publications[] = $publication;
		}
		echo $renderer->render($visible_publications);
	}
	/**
	 * Display a pubication
	 */
	function display_publication($publication_id)
	{
		$renderer = new CalendarBrowser($this);
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