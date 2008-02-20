<?php
/**
 * @package users.lib.usermanager
 */
require_once dirname(__FILE__).'/../../../main/inc/global.inc.php';
require_once dirname(__FILE__).'/../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../main/inc/lib/fileUpload.lib.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../usersdatamanager.class.php';

class RegisterForm extends FormValidator {
	
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'UserUpdated';
	const RESULT_ERROR = 'UserUpdateFailed';
	
	private $parent;
	private $user;
	private $unencryptedpass;
	private $adminDM;

	/**
	 * Creates a new RegisterForm
	 * Used for a guest to register him/herself
	 */
    function RegisterForm($user, $action) {
    	parent :: __construct('user_settings', 'post', $action);
    	
    	$this->adminDM = AdminDataManager :: get_instance();
    	$this->user = $user;
		$this->build_creation_form();
		$this->setDefaults();
    }
    
    /**
     * Creates a new basic form
     */
    function build_basic_form()
    {
    	// Lastname
		$this->addElement('text', User :: PROPERTY_LASTNAME, get_lang('LastName'));
		$this->addRule(User :: PROPERTY_LASTNAME, get_lang('ThisFieldIsRequired'), 'required');
		// Firstname
		$this->addElement('text', User :: PROPERTY_FIRSTNAME, get_lang('FirstName'));
		$this->addRule(User :: PROPERTY_FIRSTNAME, get_lang('ThisFieldIsRequired'), 'required');
		// Email
		$this->addElement('text', User :: PROPERTY_EMAIL, get_lang('Email'));
		$this->addRule(User :: PROPERTY_EMAIL, get_lang('ThisFieldIsRequired'), 'required');
		$this->addRule(User :: PROPERTY_EMAIL, get_lang('WrongEmail'), 'email');
		// Username
		$this->addElement('text', User :: PROPERTY_USERNAME, get_lang('Username'));
		$this->addRule(User :: PROPERTY_USERNAME, get_lang('ThisFieldIsRequired'), 'required');
		//pw
		$group = array();
		$group[] =& $this->createElement('radio', 'pass', null,get_lang('AutoGeneratePassword').'<br />',1);
		$group[] =& $this->createElement('radio', 'pass', null,null,0);
		$group[] =& $this->createElement('password', User :: PROPERTY_PASSWORD,null,null);
		$this->addGroup($group, 'pw', get_lang('Password'), '');
		// Official Code
		$this->addElement('text', User :: PROPERTY_OFFICIAL_CODE, get_lang('OfficialCode'));
		// Picture URI
		$this->addElement('file', User :: PROPERTY_PICTURE_URI, get_lang('AddPicture'));
		$allowed_picture_types = array ('jpg', 'jpeg', 'png', 'gif');
		$this->addRule(User :: PROPERTY_PICTURE_URI, get_lang('OnlyImagesAllowed'), 'filetype', $allowed_picture_types);
		// Phone Number
		$this->addElement('text', User :: PROPERTY_PHONE, get_lang('PhoneNumber'));
		// Language
		$adm = AdminDataManager :: get_instance();
		$languages = $adm->retrieve_languages();
		$lang_options = array();
		
		while ($language = $languages->next_result())
		{
			$lang_options[$language->get_folder()] = $language->get_english_name();	
		}
		$this->addElement('select', User :: PROPERTY_LANGUAGE, get_lang('Language'), $lang_options);
		// Status
		if ($this->adminDM->retrieve_setting_from_variable_name('allow_registration_as_teacher')->get_value() == 'true')
		{
			$status = array();  
			$status[STUDENT] = get_lang('Student');  
			$status[COURSEMANAGER]  = get_lang('CourseAdmin');
			$this->addElement('select',User :: PROPERTY_STATUS,get_lang('Status'),$status);
		}
		//  Send email 
		$group = array();
		$group[] =& $this->createElement('radio', 'send_mail',null,get_lang('Yes'),1);
		$group[] =& $this->createElement('radio', 'send_mail',null,get_lang('No'),0);
		$this->addGroup($group, 'mail', get_lang('SendMailToNewUser'), '&nbsp;'); 
		// Submit button
		$this->addElement('submit', 'user_settings', 'OK');
    }
    
    /**
     * Creates a creation form
     */
    function build_creation_form()
    {		
    	$this->build_basic_form();
    }
    
    /** 
     * Creates the user
     */
    function create_user()
    {
    	$user = $this->user;
    	$values = $this->exportValues();
    	
    	$password = $values['pw']['pass'] == '1' ? api_generate_password() : $values['pw'][User :: PROPERTY_PASSWORD];
    	if ($_FILES[User :: PROPERTY_PICTURE_URI] && file_exists($_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name']))
    	{
			$temp_picture_location = $_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name'];
			$picture_name = $_FILES[User :: PROPERTY_PICTURE_URI]['name'];
			$picture_uri = create_unique_name($picture_name);
			$picture_location = Path :: get_path(SYS_USER_PATH).$picture_uri;
			$user->set_picture_uri($picture_location);
			move_uploaded_file($temp_picture_location, $picture_location);			
    	}
		$udm = UsersDataManager :: get_instance();
    	if ($udm->is_username_available($values[User :: PROPERTY_USERNAME], $values[User :: PROPERTY_USER_ID]))
    	{
    		$user->set_user_id($values[User :: PROPERTY_USER_ID]);
    		$user->set_lastname($values[User :: PROPERTY_LASTNAME]);
    		$user->set_firstname($values[User :: PROPERTY_FIRSTNAME]);
    		$user->set_email($values[User :: PROPERTY_EMAIL]);
	    	$user->set_username($values[User :: PROPERTY_USERNAME]);
	 	   	$user->set_password(md5($password));
	 	   	$this->unencryptedpass = $password;
    		$user->set_official_code($values[User :: PROPERTY_OFFICIAL_CODE]);
  		  	$user->set_phone($values[User :: PROPERTY_PHONE]);
  		  	if ($this->adminDM->retrieve_setting_from_variable_name('allow_registration_as_teacher')->get_value() == 'false')
			{
				$values[User :: PROPERTY_STATUS] = STUDENT;
			}
			$user->set_status(intval($values[User :: PROPERTY_STATUS]));
 		   	$user->set_language($values[User :: PROPERTY_LANGUAGE]);
    		$send_mail = intval($values['mail']['send_mail']);
    		if ($send_mail)
    		{
    			$this->send_email($user); 
    		}
			if ($user->create())
			{
				PlatformSession :: platform_session_register('_uid', intval($user->get_user_id()));
				return true;
			}
			else
			{
				return false;
			}
    	}
    	else 
    	{
    		return false;
    	}
    
    }
    
	/**
	 * Sets default values. 
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$user = $this->user;
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$defaults['pw']['pass'] = 2;
			$defaults[User :: PROPERTY_DATABASE_QUOTA] = $user->get_database_quota();
			$defaults[User :: PROPERTY_DISK_QUOTA] = $user->get_disk_quota();
			$defaults[User :: PROPERTY_VERSION_QUOTA] = $user->get_version_quota();
		}
		else
		{
			$defaults['pw']['pass'] = $user->get_password();
		}
		$defaults['admin'][User :: PROPERTY_PLATFORMADMIN] = $user->get_platformadmin();
		$defaults['mail']['send_mail'] = 1;
		$defaults[User :: PROPERTY_USER_ID] = $user->get_user_id();
		$defaults[User :: PROPERTY_LASTNAME] = $user->get_lastname();
		$defaults[User :: PROPERTY_FIRSTNAME] = $user->get_firstname();
		$defaults[User :: PROPERTY_EMAIL] = $user->get_email();
		$defaults[User :: PROPERTY_USERNAME] = $user->get_username();
		$defaults[User :: PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
		$defaults[User :: PROPERTY_PICTURE_URI] = $user->get_picture_uri();
		$defaults[User :: PROPERTY_PHONE] = $user->get_phone();
		$defaults[User :: PROPERTY_LANGUAGE] = $user->get_language();
		parent :: setDefaults($defaults);
	}
	
	/**
	 * Sends an email to the registered/created user
	 */
	function send_email($user)
	{
		global $rootWeb;
		$firstname = $user->get_firstname();
		$lastname = $user->get_lastname();
		$username = $user->get_username();
		$password = $this->unencryptedpass;
		$emailto = '"'.$firstname.' '.$lastname.'" <'.$user->get_email().'>';
		$emailsubject = '['.$this->adminDM->retrieve_setting_from_variable_name('site_name')->get_value().'] '.get_lang('YourReg').' '.$this->adminDM->retrieve_setting_from_variable_name('site_name')->get_value();
		$emailheaders = 'From: '.$this->adminDM->retrieve_setting_from_variable_name('administrator_firstname')->get_value().' '.$this->adminDM->retrieve_setting_from_variable_name('administrator_surname')->get_value().' <'.$this->adminDM->retrieve_setting_from_variable_name('administrator_email')->get_value().">\n";
		$emailheaders .= 'Reply-To: '.$this->adminDM->retrieve_setting_from_variable_name('administrator_email')->get_value();
		$emailbody=get_lang('Dear')." ".stripslashes("$firstname $lastname").",\n\n".get_lang('YouAreReg')." ". $this->adminDM->retrieve_setting_from_variable_name('site_name')->get_value() ." ".get_lang('Settings')." ". $username ."\n". get_lang('Password')." : ".stripslashes($password)."\n\n" .get_lang('Address') ." ". $this->adminDM->retrieve_setting_from_variable_name('site_name')->get_value() ." ". get_lang('Is') ." : ". $rootWeb ."\n\n". get_lang('Problem'). "\n\n". get_lang('Formula').",\n\n".$this->adminDM->retrieve_setting_from_variable_name('administrator_firstname')->get_value()." ".$this->adminDM->retrieve_setting_from_variable_name('administrator_surname')->get_value()."\n". get_lang('Manager'). " ".$this->adminDM->retrieve_setting_from_variable_name('site_name')->get_value()."\nT. ".$this->adminDM->retrieve_setting_from_variable_name('administrator_telephone')->get_value()."\n" .get_lang('Email') ." : ".$this->adminDM->retrieve_setting_from_variable_name('administrator_email')->get_value();
		@api_send_mail($emailto, $emailsubject, $emailbody, $emailheaders);
	}
}
?>