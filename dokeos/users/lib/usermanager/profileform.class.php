<?php
/**
 * @package users.lib.usermanager
 */
require_once dirname(__FILE__).'/../../../main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../main/inc/lib/fileUpload.lib.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../usersdatamanager.class.php';

class ProfileForm extends FormValidator {
	
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'UserUpdated';
	const RESULT_ERROR = 'UserUpdateFailed';
	
	private $parent;
	private $user;
	private $unencryptedpass;

	/**
	 * Creates a new ProfileForm
	 */
    function ProfileForm($form_type, $user, $action) {
    	parent :: __construct('user_profile', 'post', $action);
    	
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
    	// Lastname
		$this->addElement('static', User :: PROPERTY_LASTNAME, get_lang('LastName'));
		$this->addElement('static', User :: PROPERTY_FIRSTNAME, get_lang('FirstName'));
		$this->addElement('static', User :: PROPERTY_OFFICIAL_CODE, get_lang('OfficialCode'));
		$this->addElement('static', User :: PROPERTY_EMAIL, get_lang('Email'));
		$this->addElement('static', User :: PROPERTY_USERNAME, get_lang('Username'));
		
		$languages = api_get_languages();
		$lang_options = array();
		foreach ($languages['name'] as $index => $name)
		{
			$lang_options[$languages['folder'][$index]] = $name;
		}
		$this->addElement('select', User :: PROPERTY_LANGUAGE, get_lang('Language'), $lang_options); 
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
    function update_profile()
    {
    	$user = $this->user;
    	$values = $this->exportValues();

	   	$user->set_language($values[User :: PROPERTY_LANGUAGE]);
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