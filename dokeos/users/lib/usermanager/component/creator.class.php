<?php
/**
 * @package user.usermanager
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../userform.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';

class UserManagerCreatorComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$user_id = api_get_user_id();
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserCreate'));
		if (isset($user_id) && !api_is_platform_admin()) 
		{
			$this->display_header($breadcrumbs);
			Display :: display_warning_message(get_lang('AlreadyRegistered'));
			$this->display_footer();
			exit;
		}
		$user = new User();
		$user->set_platformadmin(0);
		$user->set_password(1);
		
		$user_info = api_get_user_info();
		$user->set_creator_id($user_info['user_id']);
		
		$form = new UserForm(UserForm :: TYPE_CREATE, $user, $this->get_url());
		

		
		if($form->validate())
		{
			$success = $form->create_user();
			$this->redirect(User :: ACTION_CREATE_USER, get_lang($success ? 'UserCreated' : 'UserNotCreated'), ($success ? false : true));
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