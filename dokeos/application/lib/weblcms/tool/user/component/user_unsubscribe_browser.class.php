<?php

require_once dirname(__FILE__) . '/../user_tool.class.php';
require_once dirname(__FILE__) . '/../user_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../../weblcms_manager/component/subscribed_user_browser/subscribed_user_browser_table.class.php';

class UserToolUnsubscribeBrowserComponent extends UserToolComponent
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
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		
		echo '<br /><a name="top"></a>';
		//echo $this->perform_requested_actions();
		echo $this->action_bar->as_html();
		echo $this->get_user_unsubscribe_html();
		
		$this->display_footer();
	}
	
	function get_user_unsubscribe_html()
	{
		$table = new SubscribedUserBrowserTable($this, array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE, Weblcms :: PARAM_COURSE => $this->get_course()->get_id(), Weblcms :: PARAM_TOOL => $this->get_tool_id(), UserTool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USERS, 'application' => 'weblcms'), $this->get_unsubscribe_condition());

		$html = array();
		$html[] = $table->as_html();

		return implode($html, "\n");
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url());
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeUsers'), Theme :: get_common_img_path().'action_subscribe.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USERS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeGroups'), Theme :: get_common_img_path().'action_subscribe.png', $this->get_url(array(UserTool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_GROUPS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}
	
	function get_unsubscribe_condition()
	{
		$condition = null;

		$users = $this->get_parent()->retrieve_course_users($this->get_course());

		$conditions = array();
		while ($user = $users->next_result())
		{
			$conditions[] = new EqualityCondition(User :: PROPERTY_USER_ID, $user->get_user());
		}

		$condition = new OrCondition($conditions);

		if ($this->get_condition())
		{
			$condition = new AndCondition($condition, $this->get_condition());
		} 
		return $condition;
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