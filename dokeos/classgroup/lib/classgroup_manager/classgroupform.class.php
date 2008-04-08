<?php
require_once dirname(__FILE__).'/../../../common/global.inc.php';
require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../classgroup.class.php';
require_once dirname(__FILE__).'/../classgroupdatamanager.class.php';

class ClassGroupForm extends FormValidator {
	
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ClassGroupUpdated';
	const RESULT_ERROR = 'ClassGroupUpdateFailed';
	
	private $parent;
	private $classgroup;
	private $unencryptedpass;

    function ClassGroupForm($form_type, $classgroup, $action) {
    	parent :: __construct('classgroups_settings', 'post', $action);
    	
    	$this->classgroup = $classgroup;
    	
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
		$this->addElement('text', ClassGroup :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(ClassGroup :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addElement('html_editor', ClassGroup :: PROPERTY_DESCRIPTION, Translation :: get('Description'));
		$this->addRule(ClassGroup :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addElement('submit', 'group_settings', 'OK');
    }
    
    function build_editing_form()
    {
    	$group = $this->group;
    	$parent = $this->parent;
    	
    	$this->build_basic_form();
    	
    	$this->addElement('hidden', ClassGroup :: PROPERTY_ID);
    }
    
    function build_creation_form()
    {		
    	$this->build_basic_form();
    }
    
    function update_classgroup()
    {
    	$classgroup = $this->classgroup;
    	$values = $this->exportValues();
    	
    	$classgroup->set_name($values[ClassGroup :: PROPERTY_NAME]);
    	$classgroup->set_description($values[ClassGroup :: PROPERTY_DESCRIPTION]);
    	
   		return $classgroup->update();
    }
    
    
    
    function create_classgroup()
    {
    	$classgroup = $this->classgroup;
    	$values = $this->exportValues();
    	
    	$classgroup->set_name($values[ClassGroup :: PROPERTY_NAME]);
    	$classgroup->set_description($values[ClassGroup :: PROPERTY_DESCRIPTION]);
    	
   		return $classgroup->create();
    }
    
	/**
	 * Sets default values. 
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$classgroup = $this->classgroup;
		$defaults[ClassGroup :: PROPERTY_ID] = $classgroup->get_id();
		$defaults[ClassGroup :: PROPERTY_NAME] = $classgroup->get_name();
		$defaults[ClassGroup :: PROPERTY_DESCRIPTION] = $classgroup->get_description();
		parent :: setDefaults($defaults);
	}
}
?>