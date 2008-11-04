<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../user_form.class.php';
require_once dirname(__FILE__).'/../../user_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class UserManagerUpdaterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserUpdate')));
		$id = $_GET[UserManager :: PARAM_USER_USER_ID];
		if ($id)
		{
			$user = $this->retrieve_user($id);
		
			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header();
				Display :: display_error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			$form = new Userform(UserForm :: TYPE_EDIT, $user, $this->get_user(), $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $id)));

			if($form->validate())
			{
				$success = $form->update_user();
				$this->redirect('url', Translation :: get($success ? 'UserUpdated' : 'UserNotUpdated'), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
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
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>