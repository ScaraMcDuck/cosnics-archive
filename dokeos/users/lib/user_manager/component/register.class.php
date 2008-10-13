<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../register_form.class.php';
require_once dirname(__FILE__).'/../../users_data_manager.class.php';

class UserManagerRegisterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		if ($this->get_platform_setting('allow_registration', 'admin') == 'false')
		{
			Display :: display_not_allowed();
		}
		
		$user_id = $this->get_user_id();
		
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserRegister')));
		
		if (isset($user_id)) 
		{
			$this->display_header($trail);
			Display :: display_warning_message(Translation :: get('AlreadyRegistered'));
			$this->display_footer();
			exit;
		}
		$user = new User();
		$user->set_platformadmin(0);
		$user->set_password(1);
		//$user->set_creator_id($user_info['user_id']);
		
		$form = new RegisterForm($user, $this->get_url());
		

		
		if($form->validate())
		{
			$success = $form->create_user();
			$this->redirect('link', Translation :: get($success ? 'UserRegistered' : 'UserNotRegistered'), ($success ? false : true));
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