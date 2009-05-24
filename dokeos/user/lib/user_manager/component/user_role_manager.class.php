<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/user_role_manager_form.class.php';
require_once dirname(__FILE__).'/../../user_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class UserManagerUserRoleManagerComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION =>  UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));
		$trail->add_help('user general');

		$user_id = Request :: get(UserManager :: PARAM_USER_USER_ID);
		if(!$user_id)
		{
			$this->display_header($trail);
			$this->display_error_message('NoObjectSelected');
			$this->display_footer();
			exit();
		}

		$user = $this->retrieve_user($user_id);

        $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_USER_USER_ID => $user_id)), $user->get_fullname()));
		$trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_USER_USER_ID => $user_id)), Translation :: get('ModifyUserRoles')));

		$form = new UserRoleManagerForm($user, $this->get_user(), $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $user_id)));

		if($form->validate())
		{
			$success = $form->update_user_roles();
			$this->redirect(Translation :: get($success ? 'UserRolesChanged' : 'UserRolesNotChanged'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
		}
		else
		{
			$this->display_header($trail);

			echo sprintf(Translation :: get('ModifyRolesForUser'), $user->get_fullname());

			$form->display();
			$this->display_footer();
		}
	}
}
?>