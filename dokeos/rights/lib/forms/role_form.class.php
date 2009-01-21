<?php
/**
 * @package users.lib.usermanager
 */
require_once Path :: get_library_path() . 'html/formvalidator/FormValidator.class.php';
require_once Path :: get_rights_path() . 'lib/role.class.php';
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';

class RoleForm extends FormValidator {

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'RoleUpdated';
	const RESULT_ERROR = 'RoleUpdateFailed';

	private $parent;
	private $role;

	/**
	 * Creates a new UserForm
	 * Used by the admin to create/update a user
	 */
    function RoleForm($form_type, $role, $action) {
    	parent :: __construct('role_edit', 'post', $action);
    	
    	$this->role = $role;

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

    /**
     * Creates a basic form
     */
    function build_basic_form()
    {
    	// Lastname
		$this->addElement('text', Role :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
		$this->addRule(Role :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->add_html_editor(Role :: PROPERTY_DESCRIPTION, Translation :: get('Description'), true);
		
		// Submit button
		//$this->addElement('submit', 'user_settings', 'OK');
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Creates an editing form
     */
    function build_editing_form()
    {
    	$user = $this->user;
    	$parent = $this->parent;

    	$this->build_basic_form();

    	$this->addElement('hidden', Role :: PROPERTY_ID);
    }

    /**
     * Creates a creating form
     */
    function build_creation_form()
    {
    	$this->build_basic_form();
    }

    /**
     * Updates the user with the new data
     */
    function update_role()
    {
    	$role = $this->role;
    	$values = $this->exportValues();
    	
    	$role->set_name($values[Role :: PROPERTY_NAME]);
    	$role->set_description($values[Role :: PROPERTY_DESCRIPTION]);

   		if (!$role->update())
   		{
   			return false;
   		}
   		else
   		{
   			return true;
   		}
    }


    /**
     * Creates the user, and stores it in the database
     */
    function create_role()
    {
    	$role = $this->role;
    	$values = $this->exportValues();
    	
    	$role->set_name($values[Role :: PROPERTY_NAME]);
    	$role->set_description($values[Role :: PROPERTY_DESCRIPTION]);

   		if (!$role->create())
   		{
   			return false;
   		}
   		else
   		{
   			return true;
   		}
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$role = $this->role;
		
		$defaults[Role :: PROPERTY_NAME] = $role->get_name();
		$defaults[Role :: PROPERTY_DESCRIPTION] = $role->get_description();
			
		parent :: setDefaults($defaults);
	}
}
?>