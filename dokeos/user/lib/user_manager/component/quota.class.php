<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/user_quota_form.class.php';
require_once dirname(__FILE__).'/../../user_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class UserManagerQuotaComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$user_id = $this->get_user_id();
		
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));
		
		
		if (!$this->get_user()->is_platform_admin()) 
		{
			Display :: not_allowed();
		}
		$id = $_GET[UserManager :: PARAM_USER_USER_ID];
		if ($id)
		{
	
			$user = $this->retrieve_user($id);

            $trail->add(new Breadcrumb($this->get_url(), $user->get_fullname()));

			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header($trail, false, 'user general');
				Display :: error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}
			$form = new UserQuotaForm($user, $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $id)));

			if($form->validate())
			{
				$success = $form->update_quota();
				$this->redirect(Translation :: get($success ? 'UserQuotaUpdated' : 'UserQuotaNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => ACTION_BROWSE_USERS));
			}
			else
			{
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserQuota')));
				$this->display_header($trail, false, 'user general');
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>