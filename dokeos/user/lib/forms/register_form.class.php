<?php
/**
 * @package users.lib.usermanager
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../user_data_manager.class.php';

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
		$this->addElement('text', User :: PROPERTY_LASTNAME, Translation :: get('LastName'));
		$this->addRule(User :: PROPERTY_LASTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
		// Firstname
		$this->addElement('text', User :: PROPERTY_FIRSTNAME, Translation :: get('FirstName'));
		$this->addRule(User :: PROPERTY_FIRSTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
		// Email
		$this->addElement('text', User :: PROPERTY_EMAIL, Translation :: get('Email'));
		if (PlatformSetting :: get('require_email', 'user'))
		{
			$this->addRule(User :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
		}
		$this->addRule(User :: PROPERTY_EMAIL, Translation :: get('WrongEmail'), 'email');
		// Username
		$this->addElement('text', User :: PROPERTY_USERNAME, Translation :: get('Username'));
		$this->addRule(User :: PROPERTY_USERNAME, Translation :: get('ThisFieldIsRequired'), 'required');
		//pw
		$group = array();
		$group[] =& $this->createElement('radio', 'pass', null,Translation :: get('AutoGeneratePassword').'<br />',1);
		$group[] =& $this->createElement('radio', 'pass', null,null,0);
		$group[] =& $this->createElement('password', User :: PROPERTY_PASSWORD,null,null);
		$this->addGroup($group, 'pw', Translation :: get('Password'), '');
		// Official Code
		$this->addElement('text', User :: PROPERTY_OFFICIAL_CODE, Translation :: get('OfficialCode'));
		if (PlatformSetting :: get('require_official_code', 'user'))
		{
			$this->addRule(User :: PROPERTY_OFFICIAL_CODE, Translation :: get('ThisFieldIsRequired'), 'required');
		}
		// Picture URI
		if (PlatformSetting :: get('allow_change_user_picture', 'user'))
		{
			$this->addElement('file', User :: PROPERTY_PICTURE_URI, Translation :: get('AddPicture'));
		}
		$allowed_picture_types = array ('jpg', 'jpeg', 'png', 'gif');
		$this->addRule(User :: PROPERTY_PICTURE_URI, Translation :: get('OnlyImagesAllowed'), 'filetype', $allowed_picture_types);
		// Phone Number
		$this->addElement('text', User :: PROPERTY_PHONE, Translation :: get('PhoneNumber'));
		// Language
		$adm = AdminDataManager :: get_instance();
		$languages = $adm->retrieve_languages();
		$lang_options = array();
		
		while ($language = $languages->next_result())
		{
			$lang_options[$language->get_folder()] = $language->get_english_name();	
		}
		$this->addElement('select', User :: PROPERTY_LANGUAGE, Translation :: get('Language'), $lang_options);
		if (PlatformSetting :: get('require_language', 'user'))
		{
			$this->addRule(User :: PROPERTY_LANGUAGE, Translation :: get('ThisFieldIsRequired'), 'required');
		}
		// Status
		if (PlatformSetting :: get('allow_teacher_registration', 'user'))
		{
			$status = array();  
			$status[STUDENT] = Translation :: get('Student');  
			$status[COURSEMANAGER]  = Translation :: get('CourseAdmin');
			$this->addElement('select',User :: PROPERTY_STATUS,Translation :: get('Status'),$status);
		}
		//  Send email 
		$group = array();
		$group[] =& $this->createElement('radio', 'send_mail',null,Translation :: get('Yes'),1);
		$group[] =& $this->createElement('radio', 'send_mail',null,Translation :: get('No'),0);
		$this->addGroup($group, 'mail', Translation :: get('SendMailToNewUser'), '&nbsp;'); 
		// Submit button
		//$this->addElement('submit', 'user_settings', 'OK');
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
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
    	
    	$password = $values['pw']['pass'] == '1' ? Text :: generate_password() : $values['pw'][User :: PROPERTY_PASSWORD];
    	if ($_FILES[User :: PROPERTY_PICTURE_URI] && file_exists($_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name']))
    	{
			$temp_picture_location = $_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name'];
			$picture_name = $_FILES[User :: PROPERTY_PICTURE_URI]['name'];
			$picture_uri = create_unique_name($picture_name);
			$picture_location = Path :: get(SYS_USER_PATH).$picture_uri;
			$user->set_picture_uri($picture_location);
			move_uploaded_file($temp_picture_location, $picture_location);			
    	}
		$udm = UserDataManager :: get_instance();
    	if ($udm->is_username_available($values[User :: PROPERTY_USERNAME], $values[User :: PROPERTY_USER_ID]))
    	{
    		$user->set_id($values[User :: PROPERTY_USER_ID]);
    		$user->set_lastname($values[User :: PROPERTY_LASTNAME]);
    		$user->set_firstname($values[User :: PROPERTY_FIRSTNAME]);
    		$user->set_email($values[User :: PROPERTY_EMAIL]);
	    	$user->set_username($values[User :: PROPERTY_USERNAME]);
	 	   	$user->set_password(md5($password));
	 	   	$this->unencryptedpass = $password;
    		$user->set_official_code($values[User :: PROPERTY_OFFICIAL_CODE]);
  		  	$user->set_phone($values[User :: PROPERTY_PHONE]);
  		  	if (!PlatformSetting :: get('allow_registration_as_teacher'))
			{
				$values[User :: PROPERTY_STATUS] = STUDENT;
			}
			$user->set_status(intval($values[User :: PROPERTY_STATUS]));
 		   	$user->set_language($values[User :: PROPERTY_LANGUAGE]);
 		   	
 		   	$code = PlatformSetting :: get('days_valid');
 		   	
 		   	if ($code == 0)
			{
				$user->set_active(1);
			}
 		   	else
 		   	{
 		   		$user->set_activation_date(time());
 		   		$user->set_expiration_date(strtotime('+' . $code . ' days', time()));
 		   	}
 		   	
 		   	$user->set_registration_date(time());
    		$send_mail = intval($values['mail']['send_mail']);
    		if ($send_mail)
    		{
    			$this->send_email($user); 
    		}
			if ($user->create())
			{
				Session :: register('_uid', intval($user->get_id()));
				Events :: trigger_event('register', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $user->get_id()));
				return true;
			}
			else
			{
				return false;
			}
    	}
    	else 
    	{
    		return -1;
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
		$defaults[User :: PROPERTY_USER_ID] = $user->get_id();
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

		$subject = '['.PlatformSetting :: get('site_name').'] '.Translation :: get('YourReg').' '.PlatformSetting :: get('site_name');
		$body = Translation :: get('Dear')." ".stripslashes("$firstname $lastname").",\n\n".Translation :: get('YouAreReg')." ". PlatformSetting :: get('site_name') ." ".Translation :: get('Settings')." ". $username ."\n". Translation :: get('Password')." : ".stripslashes($password)."\n\n" .Translation :: get('Address') ." ". PlatformSetting :: get('site_name') ." ". Translation :: get('Is') ." : ". $rootWeb ."\n\n". Translation :: get('Problem'). "\n\n". Translation :: get('Formula').",\n\n".PlatformSetting :: get('administrator_firstname')." ".PlatformSetting :: get('administrator_surname')."\n". Translation :: get('Manager'). " ".PlatformSetting :: get('site_name')."\nT. ".PlatformSetting :: get('administrator_telephone')."\n" .Translation :: get('Email') ." : ".PlatformSetting :: get('administrator_email');		
		
		$mail = Mail :: factory($subject, $body, $user->get_email(), PlatformSetting :: get('administrator_email'));
		$mail->send();
		
	}
}
?>