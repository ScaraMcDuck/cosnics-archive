<?php
/**
 * @package reservations.lib.forms
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/category.class.php';

class CategoryForm extends FormValidator {

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'CategoryUpdated';
	const RESULT_ERROR = 'CategoryUpdateFailed';

	private $category;
	private $user;
	private $form_type;

	/**
	 * Creates a new LanguageForm
	 */
    function CategoryForm($form_type, $action, $category, $user) 
    {
    	parent :: __construct('category_form', 'post', $action);

		$this->category = $category;
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
    	$this->addElement('html', '<div style="float: left;width: 100%;">');
    	
    	$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">' . Translation :: get('Required') . '</span>');
    	
		// Name
		$this->addElement('text', Category :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(Category :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
		
		// Submit button
		$this->addElement('submit', 'submit', 'OK');
		
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
    	$this->addElement('hidden', Category :: PROPERTY_ID);
    }

	function create_category()
	{
		$category = $this->category;
		$category->set_name($this->exportValue(Category :: PROPERTY_NAME));
		return $category->create();
	}

    function update_category()
    {
		$category = $this->category;
		$category->set_name($this->exportValue(Category :: PROPERTY_NAME));
		return $category->update();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$category = $this->category;
		$defaults[Category :: PROPERTY_ID] = $category->get_id();
		$defaults[Category :: PROPERTY_NAME] = $category->get_name();
		parent :: setDefaults($defaults);
	}
}
?>