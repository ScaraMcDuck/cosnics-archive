<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../registerform.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';

class UserManagerRegisterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		if ($this->get_platform_setting('allow_registration', 'admin')->get_value() == 'false')
		{
			api_not_allowed();
		}
		
		$user_id = $this->get_user_id();
			
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get_lang('UserRegister'));
		if (isset($user_id)) 
		{
			$this->display_header($breadcrumbs);
			Display :: display_warning_message(Translation :: get_lang('AlreadyRegistered'));
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
			$this->redirect('link', Translation :: get_lang($success ? 'UserRegistered' : 'UserNotRegistered'), ($success ? false : true));
		}
		else
		{
			$this->display_header($breadcrumbs);
			$form->display();
			$this->display_footer();
		}
	}
}
?>