<?php
/**
 * @package users.lib.usermanager.component
 */
require_once Path :: get_rights_path() . 'lib/rights_manager/rights_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_manager/rights_manager_component.class.php';
require_once Path :: get_rights_path() . 'lib/forms/role_form.class.php';
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class RightsManagerRoleCreatorComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
        $admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('RolesAndRights')));
		$trail->add(new Breadcrumb($this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('CreateRole')));
			
		if (!$this->get_user()->is_platform_admin()) 
		{
			$this->not_allowed();
			exit;
		}
		$role = new Role();
		$role->set_user_id($this->get_user_id());
		
		$form = new RoleForm(RoleForm :: TYPE_CREATE, $role, $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_role();
			$this->redirect(Translation :: get($success ? 'RoleCreated' : 'RoleNotCreated'), ($success ? false : true), array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_BROWSE_ROLES));
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