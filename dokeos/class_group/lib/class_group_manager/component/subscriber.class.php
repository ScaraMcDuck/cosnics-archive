<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';
require_once dirname(__FILE__).'/../../class_group_data_manager.class.php';
require_once dirname(__FILE__).'/wizards/subscribe_wizard.class.php';

class ClassGroupManagerSubscriberComponent extends ClassGroupManagerComponent
{
	private $classgroup;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('Groups')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupRelUserCreate')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		
		$this->classgroup = $_GET[ClassGroupManager :: PARAM_CLASSGROUP_ID];
		
		$sw = new SubscribeWizard($this);
		$sw->run(); 
	}
	
	function get_classgroup()
	{
		return $this->classgroup;
	}
}
?>