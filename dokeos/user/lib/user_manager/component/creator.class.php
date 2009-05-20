<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/user_form.class.php';
require_once dirname(__FILE__).'/../../user_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class UserManagerCreatorComponent extends UserManagerComponent
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
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserCreate')));
			
		if (isset($user_id) && !$this->get_user()->is_platform_admin()) 
		{
			$this->display_header($trail, false, 'user general');
			Display :: warning_message(Translation :: get('AlreadyRegistered'));
			$this->display_footer();
			exit;
		}
		$user = new User();
		$user->set_platformadmin(0);
		$user->set_password(1);
		
		$user_info = $this->get_user();
		$user->set_creator_id($user_info->get_id());
		
		$form = new UserForm(UserForm :: TYPE_CREATE, $user, $this->get_user(), $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_user();
			if($success == 1)
			{
				$this->redirect(Translation :: get($success ? 'UserCreated' : 'UserNotCreated'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
			
			}
			else
			{ 
				$_GET['error_message'] = Translation :: get('UsernameNotAvailable');
				$this->display_header($trail, false, 'user general');
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_header($trail, false, 'user general');
			$form->display();
			$this->display_footer();
		}
	}
}
?>