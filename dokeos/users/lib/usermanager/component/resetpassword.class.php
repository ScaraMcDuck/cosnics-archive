<?php
/**
 * $Id$
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../registerform.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';
require_once dirname(__FILE__).'/../../../../common/authentication/authentication.class.php';
require_once dirname(__FILE__).'/../../../../common/mail/mail.class.php';
/**
 * This component can be used to reset the password of a user. The user will be
 * asked for his email-address and if the authentication source of the user
 * allows password resets, an email with further instructions will be send to
 * the user.
 */
class UserManagerResetPasswordComponent extends UserManagerComponent
{
	const PARAM_RESET_KEY = 'key';
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array ();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get_lang('LostPassword'));

		$user_id = $this->get_user_id();
		if($this->get_platform_setting('allow_password_retrieval', 'admin')->get_value() == 'false')
		{
			Display :: display_not_allowed();
		}
		if (isset ($user_id))
		{
			$this->display_header($breadcrumbs);
			Display :: display_warning_message(Translation :: get_lang('AlreadyRegistered'));
			$this->display_footer();
			exit;
		}
		$this->display_header($breadcrumbs);
		$request_key = $_GET[self::PARAM_RESET_KEY];
		$request_user_id = $_GET[User::PROPERTY_USER_ID];
		if(!is_null($request_key) && !is_null($request_user_id))
		{
			$udm = UsersDataManager :: get_instance();
			$user = $udm->retrieve_user($request_user_id);
			if($this->get_user_key($user) == $request_key)
			{
				$this->create_new_password($user);
				Display::display_normal_message('lang_your_password_has_been_emailed_to_you');
			}
			else
			{
				Display::display_error_message(Translation :: get_lang('InvalidRequest'));
			}
		}
		else
		{
		$form = new FormValidator('lost_password','post',$this->get_url());
		$form->addElement('text', User :: PROPERTY_EMAIL, Translation :: get_lang('Email'));
		$form->addRule(User :: PROPERTY_EMAIL, Translation :: get_lang('ThisFieldIsRequired'), 'required');
		$form->addRule(User :: PROPERTY_EMAIL, Translation :: get_lang('WrongEmail'), 'email');
		$form->addElement('submit', 'submit', Translation :: get_lang('Ok'));
		if ($form->validate())
		{
			$udm = UsersDataManager :: get_instance();
			$values = $form->exportValues();
			$users = $udm->retrieve_users_by_email($values[User :: PROPERTY_EMAIL]);
			if(count($users) == 0)
			{
				Display::display_error_message('NoUserWithThisEmail');
			}
			else
			{
				foreach($users as $index => $user)
				{
					$auth_source = $user->get_auth_source();
					$auth = Authentication::factory($auth_source);
					if(!$auth->is_password_changeable())
					{
						Display::display_error_message('ResetPasswordNotPossibleForThisUser');
					}
					else
					{
						$this->send_reset_link($user);
						Display::display_normal_message('ResetLinkHasBeenSend');
					}
				}
			}
		}
		else
		{
			$form->display();
		}
		}
		$this->display_footer();
	}
	/**
	 * Creates a new random password for the given user and sends an email to
	 * this user with the new password.
	 * @param User $user
	 * @return boolean True if successfull.
	 */
	private function create_new_password($user)
	{
		$password = api_generate_password();
		$user->set_password(md5($password));
		$user->update();
		$mail_subject = Translation :: get_lang('LoginRequest');
		$mail_body[] = $user->get_fullname().',';
		$mail_body[] = Translation :: get_lang('YourAccountParam').' '.$this->get_path(WEB_PATH);
		$mail_body[] = Translation :: get_lang('UserName').' :'.$user->get_username();
		$mail_body[] = Translation :: get_lang('Pass').' :'.$password;
		$mail_body = implode("\n",$mail_body);
		$mail = Mail::factory($mail_subject,$mail_body,$user->get_email());
		return $mail->send();
	}
	/**
	 * Sends an email to the user containing a reset link to request a password
	 * change.
	 * @param User $user
	 * @return boolean True if successfull.
	 */
	private function send_reset_link($user)
	{
		$url_params[self::PARAM_RESET_KEY]  = $this->get_user_key($user);
		$url_params[User::PROPERTY_USER_ID] = $user->get_user_id();
		$url = $this->get_url($url_params);
		$mail_subject = Translation :: get_lang('LoginRequest');
		$mail_body[] = $user->get_fullname().',';
		$mail_body[] = Translation :: get_lang('UserName').' :'.$user->get_username();
		$mail_body[] = Translation :: get_lang('YourAccountParam').' '.$this->get_path(WEB_PATH).': '.$url;
		$mail_body = implode("\n",$mail_body);
		$mail = Mail::factory($mail_subject,$mail_body,$user->get_email());
		return $mail->send();
	}
	/**
	 * Creates a key which is used to identify the user
	 * @param User $user
	 * @return string The requested key
	 */
	private function get_user_key($user)
	{
		global $security_key;
		return md5($security_key.$user->get_email());
	}
}
?>