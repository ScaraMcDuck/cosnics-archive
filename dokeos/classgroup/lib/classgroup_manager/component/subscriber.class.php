<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../classgroupmanager.class.php';
require_once dirname(__FILE__).'/../classgroupmanagercomponent.class.php';
require_once dirname(__FILE__).'/../../classgroupdatamanager.class.php';
require_once dirname(__FILE__).'/wizards/subscribewizard.class.php';

class ClassGroupManagerSubscriberComponent extends ClassGroupManagerComponent
{
	private $classgroup;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), 'name' => Translation :: get_lang('ClassGroups'));
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get_lang('GroupRelUserCreate'));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_error_message(Translation :: get_lang("NotAllowed"));
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