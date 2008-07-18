<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';
require_once dirname(__FILE__).'/../../class_group_data_manager.class.php';
require_once dirname(__FILE__).'/subscribe_user_browser/subscribe_user_browser_table.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ClassGroupManagerSubscribeUserBrowserComponent extends ClassGroupManagerComponent
{
	private $classgroup;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$admin = new Admin();
		$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('ClassGroupList')));
		
		$classgroup_id = $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID];
		
		if(isset($classgroup_id))
		{
			$this->classgroup = $this->retrieve_classgroup($classgroup_id);
			$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_VIEW_CLASSGROUP, ClassGroupManager :: PARAM_CLASSGROUP_ID => $classgroup_id)), $this->classgroup->get_name()));
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
		
		$this->display_header($trail);
		echo $output;
		$this->display_footer();
		
		//$sw = new SubscribeWizard($this);
		//$sw->run(); 
	}
	
	function get_user_subscribe_html()
	{
		$table = new SubscribeUserBrowserTable($this, array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_SUBSCRIBE_USER_BROWSER, ClassGroupManager :: PARAM_CLASSGROUP_ID => $this->classgroup->get_id()), $this->get_subscribe_condition());

		$html = array();
		$html[] = $table->as_html();

		return implode($html, "\n");
	}
	
	function get_subscribe_condition()
	{
		$condition = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $_GET[ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID]);
		
		$users = $this->get_parent()->retrieve_classgroup_rel_users($condition);
	
		$conditions = array();
		while ($user = $users->next_result())
		{
			$conditions[] = new NotCondition(new EqualityCondition(User :: PROPERTY_USER_ID, $user->get_user_id()));
		}

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