<?php
/**
 * @package users.lib.usermanager
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/user.class.php';
require_once dirname(__FILE__).'/user_data_manager.class.php';

class AccountForm extends FormValidator {

	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'UserUpdated';
	const RESULT_ERROR = 'UserUpdateFailed';

	private $parent;
	private $user;
	private $unencryptedpass;
	private $adm;

	/**
	 * Creates a new AccountForm
	 */
    function AccountForm($form_type, $user, $action) {
    	parent :: __construct('user_account', 'post', $action);

    	$this->user = $user;
    	$this->adm = AdminDataManager :: get_instance();

		$this->form_type = $form_type;
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}

		$this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    function build_basic_form()
    {
    	// Show user picture
    	$this->addElement('html','<img src="'.$this->user->get_full_picture_url().'" alt="'.$this->user->get_fullname().'" style="position:absolute; right: 10px; z-index:1; border:1px solid black; max-width: 150px;"/>');
    	// Name
		$this->addElement('text', User :: PROPERTY_LASTNAME, Translation :: get('LastName'));
		$this->addElement('text', User :: PROPERTY_FIRSTNAME, Translation :: get('FirstName'));
		if (PlatformSetting :: get('profile_name') !== 'true')
		{
			$this->freeze(array(User :: PROPERTY_LASTNAME,User :: PROPERTY_FIRSTNAME));
		}
		$this->applyFilter(array(User :: PROPERTY_LASTNAME, User :: PROPERTY_FIRSTNAME), 'stripslashes');
		$this->applyFilter(array(User :: PROPERTY_LASTNAME, User :: PROPERTY_FIRSTNAME), 'trim');
		$this->addRule(User :: PROPERTY_LASTNAME , Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addRule(User :: PROPERTY_FIRSTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
		// Official Code
		$this->addElement('text', User :: PROPERTY_OFFICIAL_CODE, Translation :: get('OfficialCode'));
		if (PlatformSetting :: get('profile_official_code') !== 'true')
		{
			$this->freeze(User :: PROPERTY_OFFICIAL_CODE);
		}
		$this->applyFilter(User :: PROPERTY_OFFICIAL_CODE, 'stripslashes');
		$this->applyFilter(User :: PROPERTY_OFFICIAL_CODE, 'trim');
		if (PlatformSetting :: get('registration_official_code') == 'true')
		{
			$this->addRule(User :: PROPERTY_OFFICIAL_CODE, Translation :: get('ThisFieldIsRequired'), 'required');
		}
		// Email
		$this->addElement('text', User :: PROPERTY_EMAIL, Translation :: get('Email'));
		if (PlatformSetting :: get('profile_email') !== 'true')
		{
			$this->freeze(User :: PROPERTY_EMAIL);
		}
		$this->applyFilter(User :: PROPERTY_EMAIL, 'stripslashes');
		$this->applyFilter(User :: PROPERTY_EMAIL, 'trim');
		if (PlatformSetting :: get('registration_email') == 'true')
		{
			$this->addRule(User :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
    	}
		$this->addRule(User :: PROPERTY_EMAIL, Translation :: get('EmailWrong'), 'email');
		// Username
		$this->addElement('text', User :: PROPERTY_USERNAME, Translation :: get('Username'));
		if (PlatformSetting :: get('profile_login') !== 'true')
		{
			$this->freeze(User :: PROPERTY_USERNAME);
		}
		$this->applyFilter(User :: PROPERTY_USERNAME, 'stripslashes');
		$this->applyFilter(User :: PROPERTY_USERNAME, 'trim');
		$this->addRule(User :: PROPERTY_USERNAME, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addRule(User :: PROPERTY_USERNAME, Translation :: get('UsernameWrong'), 'username');
		//Todo: The rule to check unique username should be updated to the LCMS code api
		//$this->addRule(User :: PROPERTY_USERNAME, Translation :: get('UserTaken'), 'username_available', $user_data['username']);

		// Password
		if (PlatformSetting :: get('profile_password') == 'true')
		{
			$this->addElement('static', null, null, '<em>'.Translation :: get('Enter2passToChange').'</em>');
			$this->addElement('password', User :: PROPERTY_PASSWORD, Translation :: get('Pass'),         array('size' => 40));
			$this->addElement('password', 'password2', Translation :: get('Confirmation'), array('size' => 40));
			$this->addRule(array(User :: PROPERTY_PASSWORD, 'password2'), Translation :: get('PassTwo'), 'compare');
		}
		// Picture
		if (PlatformSetting :: get('profile_picture') == 'true')
		{
			$this->addElement('file', User::PROPERTY_PICTURE_URI, ($this->user->has_picture() ? Translation :: get('UpdateImage') : Translation :: get('AddImage')));
			if($this->form_type == self :: TYPE_EDIT && $this->user->has_picture() )
			{
				$this->addElement('checkbox', 'remove_picture', null, Translation :: get('DelImage'));
			}
			$this->addRule( User::PROPERTY_PICTURE_URI, Translation :: get('OnlyImagesAllowed'), 'mimetype', array('image/gif', 'image/jpeg', 'image/png','image/x-png'));
		}
		// Language
		$adm = AdminDataManager :: get_instance();
		$languages = $adm->retrieve_languages();
		$lang_options = array();
		
		while ($language = $languages->next_result())
		{
			$lang_options[$language->get_folder()] = $language->get_english_name();	
		}
		$this->addElement('select', User :: PROPERTY_LANGUAGE, Translation :: get('Language'), $lang_options);
		if (PlatformSetting :: get('profile_language') !== 'true')
		{
			$this->freeze(User :: PROPERTY_LANGUAGE);
		}
		// Submit button
		$this->addElement('submit', 'user_settings', 'OK');
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
    	$this->build_basic_form();

    	$this->addElement('hidden', User :: PROPERTY_USER_ID);
    }

    /**
     * Builds an update form
     */
    function update_account()
    {
    	$user = $this->user;
    	$values = $this->exportValues();
		if (PlatformSetting :: get('profile_name') === 'true')
		{
			$user->set_firstname($values[User::PROPERTY_FIRSTNAME]);
			$user->set_lastname($values[User::PROPERTY_LASTNAME]);
		}
		if (PlatformSetting :: get('profile_official_code') === 'true')
		{
			$user->set_official_code($values[User :: PROPERTY_OFFICIAL_CODE]);
		}
		if (PlatformSetting :: get('profile_email') === 'true')
		{
			$user->set_email($values[User :: PROPERTY_EMAIL]);
		}
		if (PlatformSetting :: get('profile_login') === 'true')
		{
			$user->set_username($values[User :: PROPERTY_USERNAME]);
		}
		if (PlatformSetting :: get('profile_password') === 'true' && strlen($values[User :: PROPERTY_PASSWORD]))
		{
			$user->set_password(md5($values[User::PROPERTY_PASSWORD]));
		}
		if(PlatformSetting :: get('profile_picture') === 'true')
		{
			if(isset($_FILES['picture_uri']) && strlen($_FILES['picture_uri']['name']) > 0)
			{
				$user->set_picture_file($_FILES['picture_uri']);
			}
			if(isset($values['remove_picture']))
			{
				$user->delete_picture();
			}
		}
		if (PlatformSetting :: get('profile_language') === 'true')
		{
	   		$user->set_language($values[User :: PROPERTY_LANGUAGE]);
		}
   		$value = $user->update();
   		
   		if($value)
   			Events :: trigger_event('update', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $user->get_id()));
   
   		return $value;
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$user = $this->user;
		$defaults[User :: PROPERTY_USER_ID] = $user->get_id();
		$defaults[User :: PROPERTY_LASTNAME] = $user->get_lastname();
		$defaults[User :: PROPERTY_FIRSTNAME] = $user->get_firstname();
		$defaults[User :: PROPERTY_EMAIL] = $user->get_email();
		$defaults[User :: PROPERTY_USERNAME] = $user->get_username();
		$defaults[User :: PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
		$defaults[User :: PROPERTY_LANGUAGE] = $user->get_language();
		parent :: setDefaults($defaults);
	}
}
?>