<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/home_row.class.php';

class HomeRowForm extends FormValidator {
	
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';
	
	private $homerow;
	private $form_type;

    function HomeRowForm($form_type, $homerow, $action) {
    	parent :: __construct('home_row', 'post', $action);
    	
    	$this->homerow = $homerow;
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
		$this->addElement('text', HomeRow :: PROPERTY_TITLE, Translation :: get('HomeRowTitle'));
		$this->addRule(HomeRow :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('text', HomeRow :: PROPERTY_HEIGHT, Translation :: get('HomeRowHeight'));
		$this->addRule(HomeRow :: PROPERTY_HEIGHT, Translation :: get('ThisFieldIsRequired'), 'required');
				
		$this->addElement('submit', 'home_row', Translation :: get('Ok'));
    }
    
    function build_editing_form()
    {
	   	$this->build_basic_form();
    	$this->addElement('hidden', HomeRow :: PROPERTY_ID );
    }
    
    function build_creation_form()
    {
    	$this->build_basic_form();
    }
    
    function update_object()
    {
    	$homerow = $this->homerow;
    	$values = $this->exportValues();
    	
    	$homerow->set_title($values[HomeRow :: PROPERTY_TITLE]);
    	$homerow->set_height($values[HomeRow :: PROPERTY_HEIGHT]);
    	
    	return $homerow->update();
    }
    
    function create_object()
    {
    	$homerow = $this->homerow;
    	$values = $this->exportValues();
    	
    	$homerow->set_title($values[HomeRow :: PROPERTY_TITLE]);
    	$homerow->set_height($values[HomeRow :: PROPERTY_HEIGHT]);
    	
    	return $homerow->create();
    }
    
	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$homerow = $this->homerow;
		$defaults[HomeRow :: PROPERTY_ID] = $homerow->get_id();
		$defaults[HomeRow :: PROPERTY_TITLE] = $homerow->get_title();
		$defaults[HomeRow :: PROPERTY_HEIGHT] = $homerow->get_height();
		parent :: setDefaults($defaults);
	}
}
?>