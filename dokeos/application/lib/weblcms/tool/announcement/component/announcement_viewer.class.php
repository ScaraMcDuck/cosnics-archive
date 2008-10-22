<?php

require_once dirname(__FILE__) . '/../announcement_tool.class.php';
require_once dirname(__FILE__) . '/../announcement_tool_component.class.php';
require_once dirname(__FILE__) . '/announcement_viewer/announcement_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class AnnouncementToolViewerComponent extends AnnouncementToolComponent
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
		
		$browser = new AnnouncementBrowser($this);
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		
		echo '<br /><a name="top"></a>';
		//echo $this->perform_requested_actions();
		echo $this->action_bar->as_html();
		echo '<div id="action_bar_browser">';
		echo $browser->as_html();
		echo '</div>';
		
		$this->display_footer();
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url());
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_img_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_img_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_img_path().'action_edit.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_img_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
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