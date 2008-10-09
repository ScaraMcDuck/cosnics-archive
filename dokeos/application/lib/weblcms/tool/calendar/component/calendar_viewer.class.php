<?php

require_once dirname(__FILE__) . '/../calendar_tool.class.php';
require_once dirname(__FILE__) . '/../calendar_tool_component.class.php';
require_once dirname(__FILE__) . '/calendar_viewer/calendar_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class CalendarToolViewerComponent extends CalendarToolComponent
{
	private $bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		$this->bar = new ActionBarRenderer($this->get_left_actions(), $this->get_right_actions(), 
			(($_GET['view'] == 'list') || (!isset($_GET['view']))?
			$this->get_url():null));
		
		$time = isset($_GET['time']) ? intval($_GET['time']) : time();
		$this->set_parameter('time',$time);

		$browser = new CalendarBrowser($this);
		
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		
		echo '<br /><a name="top"></a>';
		//echo $this->perform_requested_actions();
		echo $this->bar->as_html() . '<br />';
		echo '<div style="width:100%; float:right;">';
		echo $browser->as_html();
		echo '</div>';
		
		$this->display_footer();
	}
	
	function get_left_actions()
	{
		$actions = array();
		
		$actions[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)),
				'label' => Translation :: get('Publish'),
				'img' => Theme :: get_common_img_path().'action_publish.png'
		);
		
		$actions[] = array(
				'href' => $this->get_url(),
				'label' => Translation :: get('Show All'),
				'img' => Theme :: get_common_img_path().'action_browser.png'
		);
		
		return $actions;
	}
	
	function get_right_actions()
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'list')),
			'img' => Theme :: get_img_path().'tool_calendar_down.png',
			'label' => Translation :: get('ListView'),
		);
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'month')),
			'img' => Theme :: get_img_path().'tool_calendar_month.png',
			'label' => Translation :: get('MonthView'),
		);
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'week')),
			'img' => Theme :: get_img_path().'tool_calendar_week.png',
			'label' => Translation :: get('WeekView'),
		);
		$toolbar_data[] = array(
			'href' => $this->get_url(array('view'=>'day')),
			'img' => Theme :: get_img_path().'tool_calendar_day.png',
			'label' => Translation :: get('DayView'),
		);
		
		return $toolbar_data;
	}
	
	function get_condition()
	{
		$query = $this->bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(LearningObject :: PROPERTY_TITLE, $query);
			$conditions[] = new LikeCondition(LearningObject :: PROPERTY_DESCRIPTION, $query);
			return new OrCondition($conditions);
		}
		
		return null;
	}
}
?>