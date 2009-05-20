<?php
require_once dirname(__FILE__).'/../rights_manager.class.php';
require_once dirname(__FILE__).'/../rights_manager_component.class.php';
require_once dirname(__FILE__).'/role_browser_table/role_browser_table.class.php';
require_once Path :: get_rights_path() . 'lib/forms/role_form.class.php';
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class RightsManagerRoleEditorComponent extends RightsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
        $admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('RolesAndRights')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('EditRole')));
		
		$id = $_GET[RightsManager :: PARAM_ROLE_ID];
		
		if ($id)
		{
			$role = $this->retrieve_role($id);
		
			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header($trail, false, 'rights general');
				Display :: error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			$form = new RoleForm(RoleForm :: TYPE_EDIT, $role, $this->get_url(array(RightsManager :: PARAM_ROLE_ID => $id)));

			if($form->validate())
			{
				$success = $form->update_role();
				$this->redirect(Translation :: get($success ? 'RoleUpdated' : 'RoleNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_BROWSE_ROLES));
			}
			else
			{
				$this->display_header($trail, false, 'rights general');
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoRoleSelected')));
		}
	}
}
?>