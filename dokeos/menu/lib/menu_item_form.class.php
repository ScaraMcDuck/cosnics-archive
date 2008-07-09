<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/menu_item.class.php';
require_once dirname(__FILE__).'/menu_item_menu.class.php';

class MenuItemForm extends FormValidator {
	
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';
	
	private $menuitem;

    function MenuItemForm($form_type, $menuitem, $action) {
    	parent :: __construct('menu_item', 'post', $action);
    	
    	$this->menuitem = $menuitem;
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
		$this->addElement('text', MenuItem :: PROPERTY_TITLE, Translation :: get('MenuItemTitle'));
		$this->addRule(MenuItem :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('select', MenuItem :: PROPERTY_APPLICATION, Translation :: get('MenuItemApplication'), $this->get_applications());
		$this->addElement('select', MenuItem :: PROPERTY_CATEGORY, Translation :: get('MenuItemParent'), $this->get_categories());
		
		$this->addElement('text', MenuItem :: PROPERTY_SECTION, Translation :: get('MenuItemSection'));
		$this->addRule(MenuItem :: PROPERTY_SECTION, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('text', MenuItem :: PROPERTY_EXTRA, Translation :: get('MenuItemExtra'));
				
		$this->addElement('submit', 'menu_item', Translation :: get('Ok'));
    }
    
    function build_editing_form()
    {
	   	$this->build_basic_form();
    	$this->addElement('hidden', MenuItem :: PROPERTY_ID );
    }
    
    function build_creation_form()
    {
    	$this->build_basic_form();
    }
    
    function update_menu_item()
    {
    	$menuitem = $this->menuitem;
    	$values = $this->exportValues();
    	
    	$menuitem->set_title($values[MenuItem :: PROPERTY_TITLE]);
    	$menuitem->set_application($values[MenuItem :: PROPERTY_APPLICATION]);
    	$menuitem->set_section($values[MenuItem :: PROPERTY_SECTION]);
    	$menuitem->set_category($values[MenuItem :: PROPERTY_CATEGORY]);
    	$menuitem->set_extra($values[MenuItem :: PROPERTY_EXTRA]);
    	
    	return $menuitem->update();
    }
    
    function create_menu_item()
    {
    	$menuitem = $this->menuitem;
    	$values = $this->exportValues();
    	
    	$menuitem->set_title($values[MenuItem :: PROPERTY_TITLE]);
    	$menuitem->set_application($values[MenuItem :: PROPERTY_APPLICATION]);
    	$menuitem->set_section($values[MenuItem :: PROPERTY_SECTION]);
    	$menuitem->set_category($values[MenuItem :: PROPERTY_CATEGORY]);
    	$menuitem->set_extra($values[MenuItem :: PROPERTY_EXTRA]);
    	
    	return $menuitem->create();
    }
    
	function get_categories()
	{
		$condition = new EqualityCondition(MenuItem :: PROPERTY_CATEGORY, 0);
		
		$items = MenuDataManager :: get_instance()->retrieve_menu_items($condition, null, null, array(MenuItem :: PROPERTY_SORT), array(SORT_ASC));
		$item_options = array();
		$item_options[0] = Translation :: get('Root');
		
		while ($item = $items->next_result())
		{
			$item_options[$item->get_id()] = $item->get_title();
		}
		return $item_options;
	}
	
	function get_applications()
	{
		$items = Application :: load_all();
		$applications = array();
		$applications[''] = Translation :: get('Root');
		
		foreach($items as $item)
		{
			$applications[$item] = Translation :: get(Application :: application_to_class($item));
		}
		return $applications;
	}
    
	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$menuitem = $this->menuitem;
		$defaults[MenuItem :: PROPERTY_TITLE] = $menuitem->get_title();
		$defaults[MenuItem :: PROPERTY_CATEGORY] = $menuitem->get_category();
		$defaults[MenuItem :: PROPERTY_APPLICATION] = $menuitem->get_application();
		$defaults[MenuItem :: PROPERTY_SECTION] = $menuitem->get_section();
		$defaults[MenuItem :: PROPERTY_EXTRA] = $menuitem->get_extra();
		parent :: setDefaults($defaults);
	}
	
	function get_menu_item()
	{
		return $this->menuitem;
	}
}
?>