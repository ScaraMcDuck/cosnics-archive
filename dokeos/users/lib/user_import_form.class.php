<?php
/**
 * @package users.lib.usermanager
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_library_path().'import/import.class.php';
require_once dirname(__FILE__).'/user.class.php';
require_once dirname(__FILE__).'/user_data_manager.class.php';

class UserImportForm extends FormValidator {
	
	const TYPE_IMPORT = 1;
	
	private $failedcsv;
	private $current_tag;
	private $current_value;
	private $user;
	private $form_user;
	private $users;
	private $udm;

	/**
	 * Creates a new UserImportForm 
	 * Used to import users from a file
	 */
    function UserImportForm($form_type, $action, $form_user) 
    {
    	parent :: __construct('user_import', 'post', $action);
    	
    	$this->form_user = $form_user;
		$this->form_type = $form_type;
		$this->failedcsv = array();
		if ($this->form_type == self :: TYPE_IMPORT)
		{
			$this->build_importing_form();
		}
    }
    
    function build_importing_form()
    {
    	$this->addElement('file', 'file', Translation :: get('FileName'));
    	$allowed_upload_types = array ('xml', 'csv');
		$this->addRule('file', Translation :: get('OnlyXMLCSVAllowed'), 'filetype', $allowed_upload_types);
    	
		$this->addElement('submit', 'user_import', Translation :: get('Ok'));
    }
    
    function import_users()
    {
    	$course = $this->course;
    	$values = $this->exportValues();
    	
    	$csvusers = $this->parse_file($_FILES['file']['tmp_name'], $_FILES['file']['type']);
    	
    	$failures = 0;
    	
    	foreach ($csvusers as $csvuser)
    	{
    		if ($this->validate_data($csvuser))
    		{
    			$user = new User();
    			
    			$user->set_firstname($csvuser[User :: PROPERTY_FIRSTNAME]);
    			$user->set_lastname($csvuser[User :: PROPERTY_LASTNAME]);
    			$user->set_username($csvuser[User :: PROPERTY_USERNAME]);
    			$user->set_password(md5($csvuser[User :: PROPERTY_PASSWORD]));
    			$user->set_email($csvuser[User :: PROPERTY_EMAIL]);
    			$user->set_language($csvuser[User :: PROPERTY_LANGUAGE]);
    			$user->set_status($csvuser[User :: PROPERTY_STATUS]);
    			//$user->set_admin(0);
    			
    			if (!$user->create())
    			{
    				$failures++;
    				$this->failedcsv[] = implode($csvuser, ';');
    			}
    			else
    			{
    				Events :: trigger_event('import', 'users', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->form_user->get_id()));
    			}
    		}
    		else
    		{
    			$failures++;
    			$this->failedcsv[] = implode($csvuser, ';');
    		}
    	}
    	
    	if ($failures > 0)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }
    
    function get_failed_csv()
    {
    	return implode($this->failedcsv, '<br />');
    }
    
    function validate_data($csvuser)
    {
    	$failures = 0;
    	$udm = $this->udm;
    	$udm = UserDataManager :: get_instance();
		
		//1. Check if username exists
		if (!$udm->is_username_available($csvuser[User :: PROPERTY_USERNAME]))
		{
			$failures++;
		}
		
		//2. Check status
		if ($csvuser[User :: PROPERTY_STATUS] != 5 && $csvuser[User :: PROPERTY_STATUS] != 1)
		{
			$failures++;
		}
		if ($failures > 0)
		{
			return false;
		}
		else
		{
    		return true;
		}
    }
    
    function parse_file($file_name, $file_type)
    {
		$this->users = array (); 
		if ($file_type == 'text/csv' || $file_type == 'application/vnd.ms-excel')
		{ 
			$this->users = Import :: csv_to_array($file_name);
		}
		elseif($file_type == 'text/xml')
		{
			$parser = xml_parser_create();
			xml_set_element_handler($parser, array (get_class(), 'element_start'), array (get_class(), 'element_end'));
			xml_set_character_data_handler($parser, array (get_class(), 'character_data'));
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
			xml_parse($parser, file_get_contents($file_name));
			xml_parser_free($parser);
		}
		return $this->users;
    }
    
	function element_start($parser, $data)
	{
		switch ($data)
		{
			case 'Contact' :
				$this->user = array ();
				break;
			default :
				$this->current_tag = $data;
		}
	}
	/**
	 * XML-parser: handle end of element
	 */
	function element_end($parser, $data)
	{
		switch ($data)
		{
			case 'Contact' :
				if ($this->user['Status'] == '5')
				{
					$this->user['Status'] = STUDENT;
				}
				if ($this->user['Status'] == '1')
				{
					$this->user['Status'] = COURSEMANAGER;
				}
				$this->users[] = $this->user;
				break;
			default :
				$this->user[$data] = $this->current_value;
				break;
		}
	}
	/**
	 * XML-parser: handle character data
	 */
	function character_data($parser, $data)
	{
		$this->current_value = $data;
	}
}
?>