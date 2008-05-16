<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../classgroupmanager.class.php';
require_once dirname(__FILE__).'/../classgroupmanagercomponent.class.php';
require_once dirname(__FILE__).'/../classgroupform.class.php';
require_once dirname(__FILE__).'/../../classgroupdatamanager.class.php';

class ClassgroupManagerCreatorComponent extends ClassgroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(ClassgroupManager :: PARAM_ACTION => ClassgroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('Groups')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ClassgroupCreate')));

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_warning_message(Translation :: get('AlreadyRegistered'));
			$this->display_footer();
			exit;
		}
		$classgroup = new Classgroup();
		$form = new ClassgroupForm(ClassgroupForm :: TYPE_CREATE, $classgroup, $this->get_url(), $this->get_user());
		
		if($form->validate())
		{
			$success = $form->create_classgroup();
			$this->redirect('url', Translation :: get($success ? 'ClassgroupCreated' : 'ClassgroupNotCreated'), ($success ? false : true), array(ClassgroupManager :: PARAM_ACTION => ClassgroupManager :: ACTION_BROWSE_CLASSGROUPS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>