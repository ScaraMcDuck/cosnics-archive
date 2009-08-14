<?php
require_once dirname(__FILE__).'/../rights_manager.class.php';
require_once dirname(__FILE__).'/../rights_manager_component.class.php';
require_once dirname(__FILE__).'/role_browser_table/role_browser_table.class.php';
require_once Path :: get_rights_path() . 'lib/forms/role_form.class.php';
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class RightsTemplateManagerEditorComponent extends RightsTemplateManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('RolesAndRights')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('EditRole')));
		$trail->add_help('rights general');

		$id = Request :: get(RightsManager :: PARAM_ROLE_ID);

		if ($id)
		{
			$role = $this->retrieve_role($id);

			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header($trail);
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
				$this->display_header($trail);
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