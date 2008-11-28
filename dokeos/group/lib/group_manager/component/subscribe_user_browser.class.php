<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../../group_data_manager.class.php';
require_once dirname(__FILE__).'/subscribe_user_browser/subscribe_user_browser_table.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class GroupManagerSubscribeUserBrowserComponent extends GroupManagerComponent
{
	private $group;
	private $ab;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
		
		$group_id = $_GET[GroupManager :: PARAM_GROUP_ID];
		
		if(isset($group_id))
		{
			$this->group = $this->retrieve_group($group_id);
			$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $group_id)), $this->group->get_name()));
		}
		
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddUsers')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		$this->ab = $this->get_action_bar();
		$output = $this->get_user_subscribe_html();
		
		$this->display_header($trail, false, false);
		echo $this->ab->as_html() . '<br />';
		echo $output;
		$this->display_footer();
	}
	
	function get_user_subscribe_html()
	{
		$table = new SubscribeUserBrowserTable($this, array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_SUBSCRIBE_USER_BROWSER, GroupManager :: PARAM_GROUP_ID => $this->group->get_id()), $this->get_subscribe_condition());

		$html = array();
		$html[] = $table->as_html();

		return implode($html, "\n");
	}
	
	function get_subscribe_condition()
	{
		$condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $_GET[GroupRelUser :: PROPERTY_GROUP_ID]);
		
		$users = $this->get_parent()->retrieve_group_rel_users($condition);
	
		$conditions = array();
		while ($user = $users->next_result())
		{
			$conditions[] = new NotCondition(new EqualityCondition(User :: PROPERTY_USER_ID, $user->get_user_id()));
		}
		
		$query = $this->ab->get_query();
		
		if(isset($query) && $query != '')
		{
			$or_conditions[] = new LikeCondition(User :: PROPERTY_FIRSTNAME, $query);
			$or_conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, $query);
			$or_conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, $query);
			$conditions[] = new OrCondition($or_conditions);
		}
		
		if(count($conditions) == 0) return null;
		
		$condition = new AndCondition($conditions);
		
		
		return $condition;
	}
	
	function get_group()
	{
		return $this->group;
	}
	
	function get_action_bar()
	{
		$group = $this->group;
		
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group->get_id())));
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		//$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowGroup'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS, GroupManager :: PARAM_GROUP_ID => $group->get_id()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
}
?>