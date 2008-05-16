<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../classgroupmanager.class.php';
require_once dirname(__FILE__).'/../classgroupmanagercomponent.class.php';
require_once dirname(__FILE__).'/../../classgroupdatamanager.class.php';
require_once dirname(__FILE__).'/wizards/subscribewizard.class.php';

class ClassgroupManagerSubscriberComponent extends ClassgroupManagerComponent
{
	private $classgroup;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(ClassgroupManager :: PARAM_ACTION => ClassgroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('Groups')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupRelUserCreate')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		
		$this->classgroup = $_GET[ClassgroupManager :: PARAM_CLASSGROUP_ID];
		
		$sw = new SubscribeWizard($this);
		$sw->run(); 
	}
	
	function get_classgroup()
	{
		return $this->classgroup;
	}
}
?>