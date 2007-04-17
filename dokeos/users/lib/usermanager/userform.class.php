<?php
require_once dirname(__FILE__).'/../../../main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../main/inc/lib/fileUpload.lib.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../usersdatamanager.class.php';

class UserForm extends FormValidator {
	
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'UserUpdated';
	const RESULT_ERROR = 'UserUpdateFailed';
	
	private $parent;
	private $user;
	private $form_user;
	private $unencryptedpass;

    function UserForm($form_type, $user, $form_user, $action) {
    	parent :: __construct('user_settings', 'post', $action);
    	
    	$this->user = $user;
    	$this->form_user = $form_user;
    	
		$this->form_type = $form_type;
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}
		
		$this->setDefaults();
    }
    
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
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$group[] =& $this->createElement('radio', 'pass', null,get_lang('KeepPassword').'<br />',2);
		}
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
		$languages = api_get_languages();
		$lang_options = array();
		foreach ($languages['name'] as $index => $name)
		{
			$lang_options[$languages['folder'][$index]] = $name;
		}
		$this->addElement('select', User :: PROPERTY_LANGUAGE, get_lang('Language'), $lang_options);
		// Disk Quota
		$this->addElement('text', User :: PROPERTY_DISK_QUOTA, get_lang('DiskQuota'));
		$this->addRule(User :: PROPERTY_DISK_QUOTA, get_lang('FieldMustBeNumeric'), 'numeric', null, 'server');
		// Database Quota
		$this->addElement('text', User :: PROPERTY_DATABASE_QUOTA, get_lang('DatabaseQuota'));
		$this->addRule(User :: PROPERTY_DATABASE_QUOTA, get_lang('FieldMustBeNumeric'), 'numeric', null, 'server');
		// Version quota
		$this->addElement('text', User :: PROPERTY_VERSION_QUOTA, get_lang('VersionQuota'));
		$this->addRule(User :: PROPERTY_VERSION_QUOTA, get_lang('FieldMustBeNumeric'), 'numeric', null, 'server');
		
		// Status
		$status = array();  
		$status[STUDENT] = get_lang('Student');  
		$status[COURSEMANAGER]  = get_lang('CourseAdmin');
		$this->addElement('select',User :: PROPERTY_STATUS,get_lang('Status'),$status);
		// Platform admin
		if ($this->user->is_platform_admin() && $this->user->get_user_id() == $this->form_user->get_user_id() && $this->form_type == self :: TYPE_EDIT)
		{
		$this->add_warning_message(null, get_lang('LockOutWarningMessage'));
		}
		$group = array();
		$group[] =& $this->createElement('radio', User :: PROPERTY_PLATFORMADMIN,null,get_lang('Yes'),1);
		$group[] =& $this->createElement('radio', User :: PROPERTY_PLATFORMADMIN,null,get_lang('No'),0);
		$this->addGroup($group, 'admin', get_lang('PlatformAdmin'), '&nbsp;');
		//  Send email 
		$group = array();
		$group[] =& $this->createElement('radio', 'send_mail',null,get_lang('Yes'),1);
		$group[] =& $this->createElement('radio', 'send_mail',null,get_lang('No'),0);
		$this->addGroup($group, 'mail', get_lang('SendMailToNewUser'), '&nbsp;'); 
		// Submit button
		$this->addElement('submit', 'user_settings', 'OK');
    }
    
    function build_editing_form()
    {
    	$user = $this->user;
    	$parent = $this->parent;
    	
    	$this->build_basic_form();
    	
    	$this->addElement('hidden', User :: PROPERTY_USER_ID);
    }
    
    function build_creation_form()
    {		
    	$this->build_basic_form();
    }
    
    function update_user()
    {
    	$user = $this->user;
    	$values = $this->exportValues();
    	
    	$password = $values['pw']['pass'] == '1' ? api_generate_password() : $values['pw'][User :: PROPERTY_PASSWORD];
    	
    	if ($_FILES[User :: PROPERTY_PICTURE_URI] && file_exists($_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name']))
    	{
			$temp_picture_location = $_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name'];
			$picture_name = $_FILES[User :: PROPERTY_PICTURE_URI]['name'];
			$picture_uri = uniqid('').'_'.replace_dangerous_char($picture_name);
			$picture_location = api_get_path(SYS_CODE_PATH).'upload/users/'.$picture_uri;
			$user->set_picture_uri($picture_location);
			move_uploaded_file($temp_picture_location, $picture_location);
    	}
		$udm = UsersDataManager :: get_instance();
    	if ($udm->is_username_available($values[User :: PROPERTY_USERNAME], $values[User :: PROPERTY_USER_ID]))
    	{
    		$user->set_lastname($values[User :: PROPERTY_LASTNAME]);
    		$user->set_firstname($values[User :: PROPERTY_FIRSTNAME]);
    		$user->set_email($values[User :: PROPERTY_EMAIL]);
	    	$user->set_username($values[User :: PROPERTY_USERNAME]);
	 	   	$user->set_password(md5($password));
	 	   	$this->unencryptedpass = $password;
    		$user->set_official_code($values[User :: PROPERTY_OFFICIAL_CODE]);
			$user->set_picture_uri($picture_uri);
  		  	$user->set_phone($values[User :: PROPERTY_PHONE]);
  		  	$user->set_status(intval($values[User :: PROPERTY_STATUS]));
 		   	$user->set_version_quota(intval($values[User :: PROPERTY_VERSION_QUOTA]));
 		   	$user->set_language($values[User :: PROPERTY_LANGUAGE]);
			$user->set_platformadmin(intval($values['admin'][User :: PROPERTY_PLATFORMADMIN]));
    		$send_mail = intval($values['mail']['send_mail']);
    		if ($send_mail)
    		{
    			$this->send_email($user); 
    		}

    		return $user->update();
    	}
    	else 
    	{
    		return false;
    	}
    }
    
    
    
    function create_user()
    {
    	$user = $this->user;
    	$values = $this->exportValues();
    	
    	$password = $values['pw']['pass'] == '1' ? api_generate_password() : $values['pw'][User :: PROPERTY_PASSWORD];
    	
    	if ($_FILES[User :: PROPERTY_PICTURE_URI] && file_exists($_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name']))
    	{
			$temp_picture_location = $_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name'];
			$picture_name = $_FILES[User :: PROPERTY_PICTURE_URI]['name'];
			$picture_uri = uniqid('').'_'.replace_dangerous_char($picture_name);
			$picture_location = api_get_path(SYS_CODE_PATH).'upload/users/'.$picture_uri;
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
			$user->set_picture_uri($picture_uri);
  		  	$user->set_phone($values[User :: PROPERTY_PHONE]);
  		  	$user->set_status(intval($values[User :: PROPERTY_STATUS]));
  		  	if ($values[User :: PROPERTY_VERSION_QUOTA] != '')
 		   	$user->set_version_quota(intval($values[User :: PROPERTY_VERSION_QUOTA]));
 		   	if ($values[User :: PROPERTY_DATABASE_QUOTA] != '')
 		   	$user->set_database_quota(intval($values[User :: PROPERTY_DATABASE_QUOTA]));
 		   	if ($values[User :: PROPERTY_DISK_QUOTA] != '')
 		   	$user->set_disk_quota(intval($values[User :: PROPERTY_DISK_QUOTA]));
 		   	$user->set_language($values[User :: PROPERTY_LANGUAGE]);
 		   	$user->set_platformadmin(intval($values['admin'][User :: PROPERTY_PLATFORMADMIN]));
    		$send_mail = intval($values['mail']['send_mail']);
    		if ($send_mail)
    		{
    			$this->send_email($user); 
    		}

    		return $user->create();
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
	
	function send_email($user)
	{
		global $rootWeb;
		$firstname = $user->get_firstname();
		$lastname = $user->get_lastname();
		$username = $user->get_username();
		$password = $this->unencryptedpass;
		$emailto = '"'.$firstname.' '.$lastname.'" <'.$user->get_email().'>';
		$emailsubject = '['.get_setting('siteName').'] '.get_lang('YourReg').' '.get_setting('siteName');
		$emailheaders = 'From: '.get_setting('administratorName').' '.get_setting('administratorSurname').' <'.get_setting('emailAdministrator').">\n";
		$emailheaders .= 'Reply-To: '.get_setting('emailAdministrator');
		$emailbody=get_lang('Dear')." ".stripslashes("$firstname $lastname").",\n\n".get_lang('YouAreReg')." ". get_setting('siteName') ." ".get_lang('Settings')." ". $username ."\n". get_lang('Password')." : ".stripslashes($password)."\n\n" .get_lang('Address') ." ". get_setting('siteName') ." ". get_lang('Is') ." : ". $rootWeb ."\n\n". get_lang('Problem'). "\n\n". get_lang('Formula').",\n\n".get_setting('administratorName')." ".get_setting('administratorSurname')."\n". get_lang('Manager'). " ".get_setting('siteName')."\nT. ".get_setting('administratorTelephone')."\n" .get_lang('Email') ." : ".get_setting('emailAdministrator');
		@api_send_mail($emailto, $emailsubject, $emailbody, $emailheaders);
	}
}
?>