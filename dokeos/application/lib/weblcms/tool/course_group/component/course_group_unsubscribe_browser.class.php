<?php

require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__).'/user_table/course_group_subscribed_user_browser_table.class.php';
require_once dirname(__FILE__).'/user_table/course_group_unsubscribed_user_browser_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class CourseGroupToolUnsubscribeBrowserComponent extends CourseGroupToolComponent
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
		$course_group = $this->get_course_group();
		$html[] = Translation :: get('Members').': '.$course_group->count_members().' / '.$course_group->get_max_number_of_members();
		$html[] = '<div style="clear: both;">&nbsp;</div>';
		if(isset($_GET[Weblcms::PARAM_USERS]))
		{
			$udm = UserDataManager :: get_instance();
			$user = $udm->retrieve_user($_GET[Weblcms :: PARAM_USERS]);
			$course_group->unsubscribe_users($user);
			$html[] = Display::display_normal_message(Translation :: get('UserUnsubscribed'),true);
		}
		$table = new CourseGroupSubscribedUserBrowserTable($this->get_parent(), array (Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id(), Weblcms :: PARAM_TOOL => $this->get_tool_id(), Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_SUBSCRIBE),$this->get_condition());
		$html[] = $this->action_bar->as_html();
		$html[] = $table->as_html();
		$this->display_header(new BreadCrumbTrail());
		echo implode($html, "\n");
		$this->display_footer();
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		//$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_img_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeUsers'), Theme :: get_common_img_path().'action_subscribe.png', $this->get_url(array (CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_SUBSCRIBE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_img_path().'action_edit.png', $this->get_url(array(CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_img_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
	
	function get_condition()
	{
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, $query);
			$conditions[] = new LikeCondition(User :: PROPERTY_FIRSTNAME, $query);
			$conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, $query);
			return new OrCondition($conditions);
		}
	}

}
?>