<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';
/**
 * This tool allows a user to publish events in his or her course.
 */
class CalendarTool extends RepositoryTool
{
	/**
	 * inherited
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
		echo '<a href="'.$this->get_url(array('view'=>'month')).'">month</a> | ';
		echo '<a href="'.$this->get_url(array('view'=>'week')).'">week</a> | ';
		echo '<a href="'.$this->get_url(array('view'=>'day')).'">day</a> | ';
		switch($_GET['view'])
		{
			case 'day':
				$this->set_parameter('view','day');
				$this->display_day_view($time);
				break;
			case 'week':
				$this->set_parameter('view','week');
				$this->display_week_view($time);
				break;
			default:
				$this->display_month_view($time);
				break;
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
		$table_date = strtotime('Monday',strtotime('-1 Week',$first_day));
		$cell = 0;
		while(date('Ym',$table_date) <= date('Ym',$time))
		{
			do
			{
				$calendar_table->setCellContents(intval($cell / 7) + 1, $cell % 7, date('d',$table_date));
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
			$calendar_table->setCellContents($hour/4+1,0,$hour.'u - '.($hour+4).'u');
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
		$events = $this->get_calendar_events($from_time,$to_time);
		foreach($events as $index => $event)
		{
			$row = date('H',strtotime($event->get_start_date()))/4+1;
			$col = date('w',strtotime($event->get_start_date()));
			$cell_contents = $calendar_table->getCellContents($row,$col);
			$cell_contents .= '<div class="event">'.$event->get_title().'</div>';
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
		$events = $this->get_calendar_events($from_time,$to_time);
		foreach($events as $index => $event)
		{
			$row = 	date('H',strtotime($event->get_start_date()))/2;
			$cell_contents = $calendar_table->getCellContents($row,0);
			$cell_contents .= '<div class="event">'.$event->get_title().'<br/>'.$event->get_description().'</div>';
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
	 *
	 */
	function get_calendar_events($from_time,$to_time)
	{
		$publications = $this->get_publications();
		$events = array();
		foreach($publications as $index => $publication)
		{
			$event = $publication->get_learning_object();
			$start_date = $event->get_start_date();
			if($from_time <= strtotime($start_date) && strtotime($start_date) <= $to_time)
			{
				$events[] = $event;
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
		$datamanager = WebLCMSDataManager :: get_instance();
		$condition = new EqualityCondition('tool','calendar');
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition);
		return $publications;
	}
}
?>