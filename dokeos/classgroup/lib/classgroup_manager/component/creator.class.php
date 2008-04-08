<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../classgroupmanager.class.php';
require_once dirname(__FILE__).'/../classgroupmanagercomponent.class.php';
require_once dirname(__FILE__).'/../classgroupform.class.php';
require_once dirname(__FILE__).'/../../classgroupdatamanager.class.php';

class ClassGroupManagerCreatorComponent extends ClassGroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), 'name' => Translation :: get('Groups'));
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('GroupCreate'));
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_warning_message(Translation :: get('AlreadyRegistered'));
			$this->display_footer();
			exit;
		}
		$classgroup = new ClassGroup();
		$form = new ClassGroupForm(ClassGroupForm :: TYPE_CREATE, $classgroup, $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_classgroup();
			$this->redirect('url', Translation :: get($success ? 'ClassGroupCreated' : 'ClassGroupNotCreated'), ($success ? false : true), array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS));
		}
		else
		{
			$this->display_header($breadcrumbs);
			$form->display();
			$this->display_footer();
		}
	}
}
?>