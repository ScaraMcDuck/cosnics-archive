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
		$user_id = $this->get_user_id();
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserCreate'));
		if (isset($user_id) && !$this->get_user()->is_platform_admin()) 
		{
			$this->display_header($breadcrumbs);
			Display :: display_warning_message(get_lang('AlreadyRegistered'));
			$this->display_footer();
			exit;
		}
		$user = new User();
		$user->set_platformadmin(0);
		$user->set_password(1);
		
		$user_info = $this->get_user();
		$user->set_creator_id($user_info->get_user_id());
		
		$form = new UserForm(UserForm :: TYPE_CREATE, $user, $this->get_user(), $this->get_url());
		

		
		if($form->validate())
		{
			$success = $form->create_user();
			$this->redirect('url', get_lang($success ? 'UserCreated' : 'UserNotCreated'), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_CREATE_USER));
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