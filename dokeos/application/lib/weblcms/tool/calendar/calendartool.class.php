<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';
/**
 * This tool allows a user to publish events in his or her course.
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
			echo '<p>Go to <a href="' . $this->get_url(array('calendaradmin' => 0)) . '">User Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'calendar_event');
			echo $pub->as_html();
		}
		else
		{
			echo '<p>Go to <a href="' . $this->get_url(array('calendaradmin' => 1)) . '">Publisher Mode</a> &hellip;</p>';
			$this->perform_requested_actions();
			if($_GET['action'] == 'delete' || $_GET['view'] == 'list')
			{
				unset($_GET['pid']);
			}
			$this->display();
		}
	}
	/**
	 * Display the list of announcements
	 */
	function display()
	{
		$time = isset($_GET['time']) ? intval($_GET['time']) : time();
		$this->set_parameter('time',$time);
		echo '<a href="'.$this->get_url(array('view'=>'list')).'">list</a> | ';
		echo '<a href="'.$this->get_url(array('view'=>'month')).'">month</a> | ';
		echo '<a href="'.$this->get_url(array('view'=>'week')).'">week</a> | ';
		echo '<a href="'.$this->get_url(array('view'=>'day')).'">day</a> <br/><br/>';
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
		$calendar_table->addRow(array ('ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'));
		$first_table_date = strtotime('Monday',strtotime('-1 Week',$first_day));
		$table_date = $first_table_date;
		$cell = 0;
		while(date('Ym',$table_date) <= date('Ym',$time))
		{
			do
			{
				$cell_contents = date('d',$table_date);
				$publications = $this->get_calendar_events($table_date,strtotime('+1 Day',$table_date));
				foreach($publications as $index => $publication)
				{
					$event = $publication->get_learning_object();
					$event_url = $this->get_url(array('pid'=>$publication->get_id()));
					$cell_contents .= '<div class="event"><a href="'.$event_url.'">'.date('H:i',$event->get_start_date()).' '.$event->get_title().'</a></div>';
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
		echo '<a href="'.$this->get_url(array('time' => $prev)).'">&lt;&lt;</a> ';
		echo date('F Y',$first_day);
		echo ' <a href="'.$this->get_url(array('time' => $next)).'">&gt;&gt;</a> ';
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
			$event_url = $this->get_url(array('pid'=>$publication->get_id()));
			$cell_contents = $calendar_table->getCellContents($row,$col);
			$cell_contents .= '<div class="event"><a href="'.$event_url.'">'.date('H:i',$event->get_start_date()).' '.$event->get_title().'</a></div>';
			$calendar_table->setCellContents($row,$col,$cell_contents);
		}
		$calendar_table->setRowType(0,'th');
		$calendar_table->setColType(0,'th');
		$prev = strtotime('-1 Week',$time);
		$next = strtotime('+1 Week',$time);
		echo '<div style="text-align: center;">';
		echo '<a href="'.$this->get_url(array('time' => $prev)).'">&lt;&lt;</a> ';
		echo get_lang('Week').' '.$week_number.' : '.date('l d M Y',$first_day).' - '.date('l d M Y',strtotime('+6 Days',$first_day));
		echo ' <a href="'.$this->get_url(array('time' => $next)).'">&gt;&gt;</a> ';
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
			$calendar_table->setCellContents($hour/2,0,$hour.'u - '.($hour+2).'u');

		}
		$from_time = mktime(0,0,0,date('m',$time),date('d',$time),date('Y',$time));
		$to_time = mktime(23,59,59,date('m',$time),date('d',$time),date('Y',$time));
		$publications = $this->get_calendar_events($from_time,$to_time);
		foreach($publications as $index => $publication)
		{
			$event = $publication->get_learning_object();
			$event_url = $this->get_url(array('pid'=>$publication->get_id()));
			$row = 	date('H',$event->get_start_date())/2;
			$cell_contents = $calendar_table->getCellContents($row,0);
			$cell_contents .= '<div class="event"><a href="'.$event_url.'">'.date('H:i',$event->get_start_date()).' '.$event->get_title().'</a><br/>'.$event->get_description().'</div>';
			$calendar_table->setCellContents($row,0,$cell_contents);
		}
		$prev = strtotime('-1 Day',$time);
		$next = strtotime('+1 Day',$time);
		echo '<div style="text-align: center;">';
		echo '<a href="'.$this->get_url(array('time' => $prev)).'">&lt;&lt;</a> ';
		echo date('l d F Y',$time);
		echo ' <a href="'.$this->get_url(array('time' => $next)).'">&gt;&gt;</a> ';
		echo '</div>';
		$calendar_table->display();
	}
	/**
	 * Display a day view of the calendar
	 * @param int $time A moment in the day to be displayed
	 */
	function display_list_view($time)
	{
		$publications = $this->get_publications();
		$events = array();
		foreach($publications as $index => $publication)
		{
			$event = $publication->get_learning_object();
			$events[$event->get_start_date()][] = $publication;
		}
		ksort($events);
		foreach($events as $time => $publications)
		{
			foreach($publications as $index => $publication)
			{
				$object = $publication->get_learning_object();
				$delete_url = $this->get_url(array('action'=>'delete','pid'=>$publication->get_id()));
				$visible_url = $this->get_url(array('action'=>'change_visibility','pid'=>$publication->get_id()));
				$visibility_img = ($publication->is_hidden() ? 'invisible.gif' : 'visible.gif');
				$html = array();
				$html[] = '<div class="learning_object">';
				$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';
				$html[] = '<div class="title">'.$object->get_title().'</div>';
				$html[] = '<div class="description">';
				$html[] = '<em>'.date('r',$object->get_start_date()).' - '.date('r',$object->get_end_date()).'</em>';
				$html[] = '<br />';
				$html[] = $object->get_description();
				$html[] = '<br />';
				$html[] = '<a href="'.$delete_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/delete.gif"/></a>';
				$html[] = '<a href="'.$visible_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$visibility_img.'"/></a>';
				$html[] = '</div>';
				$html[] = '</div>';
				$html[] = '<br /><br />';
				echo implode("\n",$html);
			}
		}
	}
	/**
	 * Display a pubication
	 */
	function display_publication($publication_id)
	{
		$datamanager = WebLCMSDataManager :: get_instance();
		$publication = $datamanager->retrieve_learning_object_publication($publication_id);
		$object = $publication->get_learning_object();
		$delete_url = $this->get_url(array('action'=>'delete','pid'=>$publication->get_id()));
		$visible_url = $this->get_url(array('action'=>'change_visibility','pid'=>$publication->get_id()));
		$visibility_img = ($publication->is_hidden() ? 'visible.gif' : 'invisible.gif');
		$html = array();
		$html[] = '<a href="'.$this->get_url().'">&laquo;&laquo; '.get_lang('Back').'</a>';
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';
		$html[] = '<div class="title">'.$object->get_title().'</div>';
		$html[] = '<div class="description">'.$object->get_description();
		$html[] = '<br />';
		$html[] = '<a href="'.$delete_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/delete.gif"/></a>';
		$html[] = '<a href="'.$visible_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$visibility_img.'"/></a>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '<br /><br />';
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
			if($from_time <= $start_date && $start_date <= $to_time)
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
		$condition = new EqualityCondition('tool','calendar');
		$this->publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition);
		return $this->publications;
	}
}
?>