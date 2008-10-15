<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../group_form.class.php';
require_once dirname(__FILE__).'/../../group_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerCreatorComponent extends GroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupCreate')));

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_warning_message(Translation :: get('AlreadyRegistered'));
			$this->display_footer();
			exit;
		}
		$classgroup = new Group();
		$form = new GroupForm(GroupForm :: TYPE_CREATE, $classgroup, $this->get_url(), $this->get_user());
		
		if($form->validate())
		{
			$success = $form->create_classgroup();
			if($success)
			{
				$classgroup = $form->get_classgroup();
				$this->redirect('url', Translation :: get('GroupCreated'), (false), array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $classgroup->get_id()));
			}
			else
			{
				$this->redirect('url', Translation :: get('GroupNotCreated'), (true), array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
			}
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