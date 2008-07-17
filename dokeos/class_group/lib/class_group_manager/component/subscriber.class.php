<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';
require_once dirname(__FILE__).'/../../class_group_data_manager.class.php';
require_once dirname(__FILE__).'/wizards/subscribe_wizard.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ClassGroupManagerSubscriberComponent extends ClassGroupManagerComponent
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
		
		$this->classgroup = $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID];
		
		if(isset($this->classgroup))
		{
			$classgroup = $this->retrieve_classgroup($this->classgroup);
			$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_VIEW_CLASSGROUP, ClassGroupManager :: PARAM_CLASSGROUP_ID => $this->classgroup)), $classgroup->get_name()));
		}
		
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupRelUserCreate')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		
		
		
		$sw = new SubscribeWizard($this);
		$sw->run(); 
	}
	
	function get_classgroup()
	{
		return $this->classgroup;
	}
}
?>