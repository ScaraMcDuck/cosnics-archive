<?php

require_once dirname(__FILE__) . '/../user_tool.class.php';
require_once dirname(__FILE__) . '/../user_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../../weblcms_manager/component/subscribed_user_browser/subscribed_user_browser_table.class.php';

class UserToolUnsubscribeBrowserComponent extends UserToolComponent
{
	private $action_bar;
	private $introduction_text;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','user'),false, null, null, 0, -1, null, new EqualityCondition('type','introduction'));
		$this->introduction_text = $publications->next_result();
		
		$this->action_bar = $this->get_action_bar();
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		
		echo '<br /><a name="top"></a>';
		//echo $this->perform_requested_actions();
		echo $this->display_introduction_text();
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

		
		if(!$this->introduction_text)
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_img_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}

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
	
	function display_introduction_text()
	{
		$html = array();
		
		$introduction_text = $this->introduction_text;
		
		if($introduction_text)
		{
			
			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_img_path() . 'action_edit.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);
			
			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_img_path() . 'action_delete.png',
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
	}
}
?>