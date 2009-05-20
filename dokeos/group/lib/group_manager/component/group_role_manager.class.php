<?php
/**
 * @package group.group_manager.component
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/group_role_manager_form.class.php';
require_once dirname(__FILE__).'/../../group_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerGroupRoleManagerComponent extends GroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION =>  GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));

		$group_id = Request :: get(GroupManager :: PARAM_GROUP_ID);
		if(!$group_id)
		{
			$this->display_header($trail, false, 'group rights');
			$this->display_error_message('NoObjectSelected');
			$this->display_footer();
			exit();
		}

		$group = $this->retrieve_group($group_id);

		$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group_id)), Translation :: get('ModifyGroupRoles')));

		$form = new GroupRoleManagerForm($group, $this->get_user(), $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group_id)));

		if($form->validate())
		{
			$success = $form->update_group_roles();
			$this->redirect(Translation :: get($success ? 'GroupRolesChanged' : 'GroupRolesNotChanged'), ($success ? false : true), array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
		}
		else
		{
			$this->display_header($trail, false, 'group rights');

			echo sprintf(Translation :: get('ModifyRolesForGroup'), $group->get_name());

			$form->display();
			$this->display_footer();
		}
	}
}
?>