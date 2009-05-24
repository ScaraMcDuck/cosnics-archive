<?php
/**
 * @package group.group_manager.component
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/group_form.class.php';
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
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateGroup')));
		$trail->add_help('group general');

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail, false);
			Display :: warning_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		$group = new Group();
		$group->set_parent($_GET[GroupManager :: PARAM_GROUP_ID]);
		$form = new GroupForm(GroupForm :: TYPE_CREATE, $group, $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $_GET[GroupManager :: PARAM_GROUP_ID])), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_group();
			if($success)
			{
				$group = $form->get_group();
				$this->redirect(Translation :: get('GroupCreated'), (false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $group->get_id()));
			}
			else
			{
				$this->redirect(Translation :: get('GroupNotCreated'), (true), array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
			}
		}
		else
		{
			$this->display_header($trail, false);
			$form->display();
			$this->display_footer();
		}
	}
}
?>