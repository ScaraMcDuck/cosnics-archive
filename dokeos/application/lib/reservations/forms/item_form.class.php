<?php
/**
 * @package reservations.lib.forms
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../item.class.php';
require_once dirname(__FILE__).'/../reservations_data_manager.class.php';

class ItemForm extends FormValidator {

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ItemUpdated';
	const RESULT_ERROR = 'ItemUpdateFailed';

	private $item;
	private $user;
	private $form_type;

	/**
	 * Creates a new LanguageForm
	 */
    function ItemForm($form_type, $action, $item, $user) 
    {
    	parent :: __construct('item_form', 'post', $action);

		$this->item = $item;
		$this->user = $user;
		$this->form_type = $form_type;
		
		$this->build_basic_form();
		
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
    	//$this->addElement('html', '<div style="float: left;width: 100%;">');
    	
    	$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">' . Translation :: get('Required') . '</span>');
    	
		// Name
		$this->addElement('text', Item :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(Item :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->add_html_editor(Item :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
		
		
		$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">' . Translation :: get('Optional') . '</span>');
    	
		$this->addElement('text', Item :: PROPERTY_RESPONSIBLE, Translation :: get('Responsible'));
		
		/*$users = UserDataManager :: get_instance()->retrieve_users(new EqualityCondition(User :: PROPERTY_RESPONSIBLE, 1));
		while($user = $users->next_result())
		{
			$select_users[$user->get_id()] = $user->get_fullname();
		}
		
		$this->addElement('select', Item :: PROPERTY_RESPONSIBLE, Translation :: get('Responsible'), $select_users);*/
		
		$this->addElement('text', Item :: PROPERTY_CREDITS, Translation :: get('CreditsPerHour'));
		$this->addElement('checkbox', Item :: PROPERTY_BLACKOUT, null, Translation :: get('Blackout'));
		
		//$this->addElement('text', Item :: PROPERTY_SALTO_ID, Translation :: get('SaltoId'));
		
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
		
		// Submit button
		$this->addElement('submit', 'submit', 'OK');
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
    	$this->addElement('hidden', Item :: PROPERTY_ID);
    }

	function create_item()
	{
		$item = $this->item;
		$item->set_name($this->exportValue(Item :: PROPERTY_NAME));
		$item->set_description($this->exportValue(Item :: PROPERTY_DESCRIPTION));
		$item->set_responsible($this->exportValue(Item :: PROPERTY_RESPONSIBLE));
		
		$cred = $this->exportValue(Item :: PROPERTY_CREDITS);
		$item->set_credits($cred?$cred:0);
		
		$bo = $this->exportValue(Item :: PROPERTY_BLACKOUT);
		$item->set_blackout($bo?$bo:0);
		
		$item->set_salto_id($this->exportValue(Item :: PROPERTY_SALTO_ID));
		
		return $item->create();
	}

    function update_item()
    {
		$item = $this->item;
		$item->set_name($this->exportValue(Item :: PROPERTY_NAME));
		$item->set_description($this->exportValue(Item :: PROPERTY_DESCRIPTION));
		$item->set_responsible($this->exportValue(Item :: PROPERTY_RESPONSIBLE));
		
		$cred = $this->exportValue(Item :: PROPERTY_CREDITS);
		$item->set_credits($cred?$cred:0);
		
		$bo = $this->exportValue(Item :: PROPERTY_BLACKOUT);
		$item->set_blackout($bo?$bo:0);
		
		$item->set_salto_id($this->exportValue(Item :: PROPERTY_SALTO_ID));
		
		return $item->update();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$item = $this->item;
		$defaults[Item :: PROPERTY_ID] = $item->get_id();
		$defaults[Item :: PROPERTY_NAME] = $item->get_name();
		$defaults[Item :: PROPERTY_DESCRIPTION] = $item->get_description();
		$defaults[Item :: PROPERTY_RESPONSIBLE] = $item->get_responsible() ? $item->get_responsible() : $this->user->get_fullname();
		$defaults[Item :: PROPERTY_CREDITS] = $item->get_credits() ? $item->get_credits() : 0;
		$defaults[Item :: PROPERTY_BLACKOUT] = $item->get_blackout();
		parent :: setDefaults($defaults);
	}
}
?>