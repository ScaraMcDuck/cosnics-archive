<?php

require_once dirname(__FILE__) . '/../calendar_tool.class.php';
require_once dirname(__FILE__) . '/../calendar_tool_component.class.php';
require_once dirname(__FILE__) . '/calendar_viewer/calendar_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class CalendarToolViewerComponent extends CalendarToolComponent
{
	private $action_bar;
	private $introduction_text;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','calendar'),false, null, null, 0, -1, null, new EqualityCondition('type','introduction'));
		$this->introduction_text = $publications->next_result();
		$this->action_bar = $this->get_action_bar();

		$time = isset($_GET['time']) ? intval($_GET['time']) : time();
		$view = isset ($_GET['view']) ? $_GET['view'] : 'month';
		$this->set_parameter('time',$time);
		$this->set_parameter('view', $view);
		$browser = new CalendarBrowser($this);
		
		$trail = new BreadcrumbTrail();

        if(Request :: get('view') != null)
        {
            if(Request :: get('today') == 1)
            {
                $title = 'Today';
                $trail->add(new BreadCrumb($this->get_url(array('today' => 1)), $title));
            }
            else
            {
                $title = Translation :: get(ucfirst(Request :: get('view')).'View');
                $trail->add(new BreadCrumb($this->get_url(), $title));
            }
        }
        
		$this->display_header($trail);
		echo '<br /><a name="top"></a>';
		if(!isset($_GET['pid']))
		{
			if(PlatformSetting :: get('enable_introduction', 'weblcms'))
			{
				echo $this->display_introduction_text($this->introduction_text);
			}
		}
		echo $this->action_bar->as_html() . '<br />';
		echo '<div id="action_bar_browser">';
		echo $browser->as_html();
		echo '</div>';
		
		$this->display_footer();
	}
	
	function add_actionbar_item($item)
	{
		$this->action_bar->add_tool_action($item);
	}
			
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		if(!isset($_GET['pid']))
		{
			$action_bar->set_search_url(($_GET['view'] == 'list') ? $this->get_url(array('view' => $_GET['view'])) : null);
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		
		if($_GET['view'] == 'list')
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array('view' => 'list')), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		
		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		
		if(!isset($_GET['pid']))
		{
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_image_path().'tool_calendar_down.png', $this->get_url(array('view'=>'list', 'time' => $_GET['time'])), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('MonthView'), Theme :: get_image_path().'tool_calendar_month.png', $this->get_url(array('view'=>'month', 'time' => $_GET['time'])), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('WeekView'), Theme :: get_image_path().'tool_calendar_week.png', $this->get_url(array('view'=>'week', 'time' => $_GET['time'])), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('DayView'), Theme :: get_image_path().'tool_calendar_day.png', $this->get_url(array('view'=>'day', 'time' => $_GET['time'])), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Today'), Theme :: get_image_path().'tool_calendar_today.png', $this->get_url(array('view' => (isset ($_GET['view']) ? $_GET['view'] : 'month'), 'time' => time(), 'today' => true)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		$action_bar->set_help_action(HelpManager :: get_tool_bar_help_item('calendar tool'));
        
        $action_bar->add_tool_action($this->get_access_details_toolbar_item($this));
        
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
	
	/*function display_introduction_text()
	{
		$html = array();
		
		$introduction_text = $this->introduction_text;
		
		if($introduction_text)
		{
			
			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path() . 'action_edit.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);
			
			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);
			
			$html[] = '<div class="learning_object">';
			$html[] = '<div class="description">';
			$html[] = $introduction_text->get_learning_object()->get_description();
			$html[] = '</div>';
			$html[] = DokeosUtilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
			$html[] = '</div>';
			$html[] = '<br />';
		}
		
		return implode("\n",$html);
	}*/
}
?>