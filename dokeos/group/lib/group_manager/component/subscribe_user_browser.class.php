<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../../group_data_manager.class.php';
require_once dirname(__FILE__).'/subscribe_user_browser/subscribe_user_browser_table.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerSubscribeUserBrowserComponent extends GroupManagerComponent
{
	private $classgroup;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
		
		$classgroup_id = $_GET[GroupManager :: PARAM_GROUP_ID];
		
		if(isset($classgroup_id))
		{
			$this->classgroup = $this->retrieve_classgroup($classgroup_id);
			$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $classgroup_id)), $this->classgroup->get_name()));
		}
		
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddUsers')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		
		$output = $this->get_user_subscribe_html();
		
		$this->display_header($trail, false, true);
		$this->display_user_search_form();
		echo $output;
		$this->display_footer();
		
		//$sw = new SubscribeWizard($this);
		//$sw->run(); 
	}
	
	function get_user_subscribe_html()
	{
		$table = new SubscribeUserBrowserTable($this, array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_SUBSCRIBE_USER_BROWSER, GroupManager :: PARAM_GROUP_ID => $this->classgroup->get_id()), $this->get_subscribe_condition());

		$html = array();
		$html[] = $table->as_html();

		return implode($html, "\n");
	}
	
	function get_subscribe_condition()
	{
		$condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $_GET[GroupRelUser :: PROPERTY_GROUP_ID]);
		
		$users = $this->get_parent()->retrieve_classgroup_rel_users($condition);
	
		$conditions = array();
		while ($user = $users->next_result())
		{
			$conditions[] = new NotCondition(new EqualityCondition(User :: PROPERTY_USER_ID, $user->get_user_id()));
		}
		
		$user_search_condition = $this->get_user_search_condition();
		
		if (count($user_search_condition))
			$conditions[] = $user_search_condition;
		
		if(count($conditions) == 0) return null;
		
		$condition = new AndCondition($conditions);
		
		return $condition;
	}
	
	function get_classgroup()
	{
		return $this->classgroup;
	}
}
?>