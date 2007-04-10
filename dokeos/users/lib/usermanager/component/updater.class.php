<?php

require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../userform.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';

class UserManagerUpdaterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserCreate'));
		$id = $_GET[UserManager :: PARAM_USER_USER_ID];
		$user = $this->retrieve_user($id);
		
		if (!api_is_platform_admin())
		{
			$this->display_header();
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$form = new Userform(UserForm :: TYPE_EDIT, $user, $this->get_url());

		if($form->validate())
		{
			$success = $form->create_user();
			$this->redirect(User :: ACTION_CREATE_USER, get_lang($success ? 'UserUpdated' : 'UserNotUpdated'), ($success ? false : true));
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