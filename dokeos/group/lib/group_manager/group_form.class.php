<?php
require_once dirname(__FILE__).'/../../../common/global.inc.php';
require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../group.class.php';
require_once dirname(__FILE__).'/../group_data_manager.class.php';

class GroupForm extends FormValidator {
	
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'GroupUpdated';
	const RESULT_ERROR = 'GroupUpdateFailed';
	
	private $parent;
	private $classgroup;
	private $unencryptedpass;
	private $user;

    function GroupForm($form_type, $classgroup, $action, $user) {
    	parent :: __construct('classgroups_settings', 'post', $action);
    	
    	$this->classgroup = $classgroup;
    	$this->user = $user;
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
		$this->addElement('text', Group :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(Group :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addElement('html_editor', Group :: PROPERTY_DESCRIPTION, Translation :: get('Description'));
		$this->addRule(Group :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addElement('submit', 'group_settings', 'OK');
    }
    
    function build_editing_form()
    {
    	$group = $this->group;
    	$parent = $this->parent;
    	
    	$this->build_basic_form();
    	
    	$this->addElement('hidden', Group :: PROPERTY_ID);
    }
    
    function build_creation_form()
    {		
    	$this->build_basic_form();
    }
    
    function update_classgroup()
    {
    	$classgroup = $this->classgroup;
    	$values = $this->exportValues();
    	
    	$classgroup->set_name($values[Group :: PROPERTY_NAME]);
    	$classgroup->set_description($values[Group :: PROPERTY_DESCRIPTION]);
    	
   		$value = $classgroup->update();
   		
   		if($value)
   			Events :: trigger_event('update', 'group', array('target_group_id' => $classgroup->get_id(), 'action_user_id' => $this->user->get_id()));
   		
   		return $value;
    }
    
    
    
    function create_classgroup()
    {
    	$classgroup = $this->classgroup;
    	$values = $this->exportValues();
    	
    	$classgroup->set_name($values[Group :: PROPERTY_NAME]);
    	$classgroup->set_description($values[Group :: PROPERTY_DESCRIPTION]);
    	
   		$value = $classgroup->create();
   		
   		if($value)
   			Events :: trigger_event('create', 'group', array('target_group_id' => $classgroup->get_id(), 'action_user_id' => $this->user->get_id()));
   			
   		
   		return $value;
    }
    
	/**
	 * Sets default values. 
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$classgroup = $this->classgroup;
		$defaults[Group :: PROPERTY_ID] = $classgroup->get_id();
		$defaults[Group :: PROPERTY_NAME] = $classgroup->get_name();
		$defaults[Group :: PROPERTY_DESCRIPTION] = $classgroup->get_description();
		parent :: setDefaults($defaults);
	}
	
	function get_classgroup()
	{
		return $this->classgroup;
	}
}
?>