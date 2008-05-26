<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../class_group_manager.class.php';
require_once dirname(__FILE__).'/../class_group_manager_component.class.php';
require_once dirname(__FILE__).'/../class_group_form.class.php';
require_once dirname(__FILE__).'/../../class_group_data_manager.class.php';

class ClassGroupManagerCreatorComponent extends ClassGroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS)), Translation :: get('Groups')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ClassGroupCreate')));

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_warning_message(Translation :: get('AlreadyRegistered'));
			$this->display_footer();
			exit;
		}
		$classgroup = new ClassGroup();
		$form = new ClassGroupForm(ClassGroupForm :: TYPE_CREATE, $classgroup, $this->get_url(), $this->get_user());
		
		if($form->validate())
		{
			$success = $form->create_classgroup();
			$this->redirect('url', Translation :: get($success ? 'ClassGroupCreated' : 'ClassGroupNotCreated'), ($success ? false : true), array(ClassGroupManager :: PARAM_ACTION => ClassGroupManager :: ACTION_BROWSE_CLASSGROUPS));
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