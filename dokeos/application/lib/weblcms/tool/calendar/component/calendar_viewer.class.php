<?php

require_once dirname(__FILE__) . '/../calendar_tool.class.php';
require_once dirname(__FILE__) . '/../calendar_tool_component.class.php';
require_once dirname(__FILE__) . '/calendar_viewer/calendar_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class CalendarToolViewerComponent extends CalendarToolComponent
{
	private $action_bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$this->action_bar = $this->get_action_bar();

		$time = isset($_GET['time']) ? intval($_GET['time']) : time();
		$view = isset ($_GET['view']) ? $_GET['view'] : 'month';
		$this->set_parameter('time',$time);
		$this->set_parameter('view', $view);
		$browser = new CalendarBrowser($this);
		
		$trail = new BreadcrumbTrail();
		$this->display_header($trail);
		echo '<br /><a name="top"></a>';
		echo $this->action_bar->as_html() . '<br />';
		echo '<div id="action_bar_browser">';
		echo $browser->as_html();
		echo '</div>';
		
		$this->display_footer();
	}
			
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url(($_GET['view'] == 'list') ? $this->get_url(array('view' => $_GET['view'])) : null);
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_img_path().'action_publish.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		if($_GET['view'] == 'list')
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_img_path().'action_browser.png', $this->get_url(array('view' => 'list')), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_img_path().'tool_calendar_down.png', $this->get_url(array('view'=>'list', 'time' => $_GET['time'])), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('MonthView'), Theme :: get_img_path().'tool_calendar_month.png', $this->get_url(array('view'=>'month', 'time' => $_GET['time'])), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('WeekView'), Theme :: get_img_path().'tool_calendar_week.png', $this->get_url(array('view'=>'week', 'time' => $_GET['time'])), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('DayView'), Theme :: get_img_path().'tool_calendar_day.png', $this->get_url(array('view'=>'day', 'time' => $_GET['time'])), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Today'), Theme :: get_img_path().'tool_calendar_today.png', $this->get_url(array('view' => (isset ($_GET['view']) ? $_GET['view'] : 'month'), 'time' => time())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
	
	function get_condition()
	{
		$query = $this->action_bar->get_query();
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