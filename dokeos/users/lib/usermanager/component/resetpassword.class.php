<?php

/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../registerform.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';
require_once dirname(__FILE__).'/../../../../common/authentication/authentication.class.php';

class UserManagerResetPasswordComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array ();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('LostPassword'));

		$user_id = $this->get_user_id();
		if( get_setting('allow_lostpassword') == 'false')
		{
			api_not_allowed();
			exit;
		}
		if (isset ($user_id))
		{
			$this->display_header($breadcrumbs);
			Display :: display_warning_message(get_lang('AlreadyRegistered'));
			$this->display_footer();
			exit;
		}

		$this->display_header($breadcrumbs);

		$form = new FormValidator('lost_password','post',$this->get_url());
		$form->addElement('text', User :: PROPERTY_EMAIL, get_lang('Email'));
		$form->addRule(User :: PROPERTY_EMAIL, get_lang('ThisFieldIsRequired'), 'required');
		$form->addRule(User :: PROPERTY_EMAIL, get_lang('WrongEmail'), 'email');
		$form->addElement('submit', 'submit', get_lang('Ok'));
		if ($form->validate())
		{
			$udm = UsersDataManager :: get_instance();
			$values = $form->exportValues();
			$user = $udm->retrieve_user_by_email($values[User :: PROPERTY_EMAIL]);
			if(is_null($user))
			{
				Display::display_error_message('NoUserWithThisEmail');
			}
			else
			{
				$auth_source = $user->get_auth_source();
				$auth = Authentication::factory($auth_source);
				if(!$auth->is_password_changeable())
				{
					Display::display_error_message('ResetPasswordNotPossibleForThisUser');
				}
				else
				{
					//todo: Code to send email & reset password;
					Display::display_normal_message('TODO');
				}
			}
		}
		else
		{
			$form->display();
		}

		$this->display_footer();
	}
}
?>