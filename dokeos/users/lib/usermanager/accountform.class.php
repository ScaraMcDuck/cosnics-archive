<?php
/**
 * @package users.lib.usermanager
 */
require_once dirname(__FILE__).'/../../../main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../main/inc/lib/fileUpload.lib.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../usersdatamanager.class.php';

class AccountForm extends FormValidator {

	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'UserUpdated';
	const RESULT_ERROR = 'UserUpdateFailed';

	private $parent;
	private $user;
	private $unencryptedpass;

	/**
	 * Creates a new AccountForm
	 */
    function AccountForm($form_type, $user, $action) {
    	parent :: __construct('user_account', 'post', $action);

    	$this->user = $user;

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
		$this->addElement('text', User :: PROPERTY_LASTNAME, get_lang('LastName'));
		$this->addElement('text', User :: PROPERTY_FIRSTNAME, get_lang('FirstName'));
		if (api_get_setting('profile', 'name') !== 'true')
		{
			$this->freeze(array(User :: PROPERTY_LASTNAME,User :: PROPERTY_FIRSTNAME));
		}
		$this->applyFilter(array(User :: PROPERTY_LASTNAME, User :: PROPERTY_FIRSTNAME), 'stripslashes');
		$this->applyFilter(array(User :: PROPERTY_LASTNAME, User :: PROPERTY_FIRSTNAME), 'trim');
		$this->addRule(User :: PROPERTY_LASTNAME , get_lang('ThisFieldIsRequired'), 'required');
		$this->addRule(User :: PROPERTY_FIRSTNAME, get_lang('ThisFieldIsRequired'), 'required');
		// Official Code
		$this->addElement('text', User :: PROPERTY_OFFICIAL_CODE, get_lang('OfficialCode'));
		if (api_get_setting('profile', 'officialcode') !== 'true')
		{
			$this->freeze(User :: PROPERTY_OFFICIAL_CODE);
		}
		$this->applyFilter(User :: PROPERTY_OFFICIAL_CODE, 'stripslashes');
		$this->applyFilter(User :: PROPERTY_OFFICIAL_CODE, 'trim');
		if (api_get_setting('registration', 'officialcode') == 'true')
		{
			$this->addRule(User :: PROPERTY_OFFICIAL_CODE, get_lang('ThisFieldIsRequired'), 'required');
		}
		// Email
		$this->addElement('text', User :: PROPERTY_EMAIL, get_lang('Email'));
		if (api_get_setting('profile', 'email') !== 'true')
		{
			$this->freeze(User :: PROPERTY_EMAIL);
		}
		$this->applyFilter(User :: PROPERTY_EMAIL, 'stripslashes');
		$this->applyFilter(User :: PROPERTY_EMAIL, 'trim');
		if (api_get_setting('registration', 'email') == 'true')
		{
			$this->addRule(User :: PROPERTY_EMAIL, get_lang('ThisFieldIsRequired'), 'required');
    	}
		$this->addRule(User :: PROPERTY_EMAIL, get_lang('EmailWrong'), 'email');
		// Username
		$this->addElement('text', User :: PROPERTY_USERNAME, get_lang('Username'));
		if (api_get_setting('profile', 'login') !== 'true')
		{
			$this->freeze(User :: PROPERTY_USERNAME);
		}
		$this->applyFilter(User :: PROPERTY_USERNAME, 'stripslashes');
		$this->applyFilter(User :: PROPERTY_USERNAME, 'trim');
		$this->addRule(User :: PROPERTY_USERNAME, get_lang('ThisFieldIsRequired'), 'required');
		$this->addRule(User :: PROPERTY_USERNAME, get_lang('UsernameWrong'), 'username');
		//Todo: The rule to check unique username should be updated to the LCMS code api
		//$this->addRule(User :: PROPERTY_USERNAME, get_lang('UserTaken'), 'username_available', $user_data['username']);

		// Password
		if (api_get_setting('profile', 'password') == 'true')
		{
			$this->addElement('static', null, null, '<em>'.get_lang('Enter2passToChange').'</em>');
			$this->addElement('password', User :: PROPERTY_PASSWORD, get_lang('Pass'),         array('size' => 40));
			$this->addElement('password', 'password2', get_lang('Confirmation'), array('size' => 40));
			$this->addRule(array(User :: PROPERTY_PASSWORD, 'password2'), get_lang('PassTwo'), 'compare');
		}
		// Picture
		if (api_get_setting('profile', 'picture') == 'true')
		{
			$this->addElement('file', User::PROPERTY_PICTURE_URI, ($this->user->has_picture() ? get_lang('UpdateImage') : get_lang('AddImage')));
			if($this->form_type == self :: TYPE_EDIT && $this->user->has_picture() )
			{
				$this->addElement('checkbox', 'remove_picture', null, get_lang('DelImage'));
			}
			$this->addRule( User::PROPERTY_PICTURE_URI, get_lang('OnlyImagesAllowed'), 'mimetype', array('image/gif', 'image/jpeg', 'image/png'));
		}
		// Language
		$languages = api_get_languages();
		$lang_options = array();
		foreach ($languages['name'] as $index => $name)
		{
			$lang_options[$languages['folder'][$index]] = $name;
		}
		$this->addElement('select', User :: PROPERTY_LANGUAGE, get_lang('Language'), $lang_options);
		if (api_get_setting('profile', 'language') !== 'true')
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
		if (api_get_setting('profile', 'name') === 'true')
		{
			$user->set_firstname($values[User::PROPERTY_FIRSTNAME]);
			$user->set_lastname($values[User::PROPERTY_LASTNAME]);
		}
		if (api_get_setting('profile', 'officialcode') === 'true')
		{
			$user->set_official_code($values[User :: PROPERTY_OFFICIAL_CODE]);
		}
		if (api_get_setting('profile', 'email') === 'true')
		{
			$user->set_email($values[User :: PROPERTY_EMAIL]);
		}
		if (api_get_setting('profile', 'login') === 'true')
		{
			$user->set_username($values[User :: PROPERTY_USERNAME]);
		}
		if (api_get_setting('profile', 'password') === 'true' && strlen($values[User :: PROPERTY_PASSWORD]))
		{
			$user->set_password($values[User::PROPERTY_PASSWORD]);
		}
		if(api_get_setting('profile', 'picture') === 'true')
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
		if (api_get_setting('profile', 'language') === 'true')
		{
	   		$user->set_language($values[User :: PROPERTY_LANGUAGE]);
		}
   		return $user->update();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$user = $this->user;
		$defaults[User :: PROPERTY_USER_ID] = $user->get_user_id();
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