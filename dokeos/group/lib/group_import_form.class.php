<?php
/**
 * @package users.lib.usermanager
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_library_path().'import/import.class.php';
require_once dirname(__FILE__).'/group.class.php';
require_once dirname(__FILE__).'/group_data_manager.class.php';

class GroupImportForm extends FormValidator {
	
	const TYPE_IMPORT = 1;
	
	private $failedcsv;
	private $current_tag;
	private $current_value;
	private $group;
	private $form_group;
	private $groups;
	private $udm;

	/**
	 * Creates a new GroupImportForm 
	 * Used to import groups from a file
	 */
    function GroupImportForm($form_type, $action, $form_group) 
    {
    	parent :: __construct('group_import', 'post', $action);
    	
    	$this->form_group = $form_group;
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
    	
		$this->addElement('submit', 'group_import', Translation :: get('Ok'));
    }
    
    function import_groups()
    {
    	$course = $this->course;
    	$values = $this->exportValues();
    	
    	$csvgroups = $this->parse_file($_FILES['file']['tmp_name'], $_FILES['file']['type']);
    	
    	$failures = 0;
    	
    	foreach ($csvgroups as $csvgroup)
    	{
    		if ($this->validate_data($csvgroup))
    		{
    			$group = new Group();
    			
    			/*$group->set_firstname($csvgroup[Group :: PROPERTY_FIRSTNAME]);
    			$group->set_lastname($csvgroup[Group :: PROPERTY_LASTNAME]);
    			$group->set_groupname($csvgroup[Group :: PROPERTY_USERNAME]);
    			$group->set_password(md5($csvgroup[Group :: PROPERTY_PASSWORD]));
    			$group->set_email($csvgroup[Group :: PROPERTY_EMAIL]);
    			$group->set_language($csvgroup[Group :: PROPERTY_LANGUAGE]);
    			$group->set_status($csvgroup[Group :: PROPERTY_STATUS]);*/
    			//$group->set_admin(0);
    			//$group->set_
    			
    			if (!$group->create())
    			{
    				$failures++;
    				$this->failedcsv[] = implode($csvgroup, ';');
    			}
    			else
    			{
    				//Events :: trigger_event('import', 'group', array('target_group_id' => $group->get_id(), 'action_group_id' => $this->form_group->get_id()));
    			}
    		}
    		else
    		{
    			$failures++;
    			$this->failedcsv[] = implode($csvgroup, ';');
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
    
    function validate_data($csvgroup)
    {
    	return true;
    }
    
    function parse_file($file_name, $file_type)
    {
		$this->groups = array (); 
		if ($file_type == 'text/csv' || $file_type == 'application/vnd.ms-excel')
		{ 
			$this->groups = Import :: csv_to_array($file_name);
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
		return $this->groups;
    }
    
	function element_start($parser, $data)
	{
		switch ($data)
		{
			case 'Contact' :
				$this->group = array ();
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
				$this->groups[] = $this->group;
				break;
			default :
				$this->group[$data] = $this->current_value;
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