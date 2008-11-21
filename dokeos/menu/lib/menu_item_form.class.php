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
		$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">'. Translation :: get('Main') .'</span>');
		$this->addElement('text', MenuItem :: PROPERTY_TITLE, Translation :: get('MenuItemTitle'));
		$this->addRule(MenuItem :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('select', MenuItem :: PROPERTY_CATEGORY, Translation :: get('MenuItemParent'), $this->get_categories());
		$this->addRule(MenuItem :: PROPERTY_CATEGORY, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
		
		$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">'. Translation :: get('Link') .'</span>');
		
		$choices[] = $this->createElement('radio','app','',Translation :: get('Application'),0,array ('onclick' => 'javascript:application_clicked()'));
		$choices[] = $this->createElement('radio','app','',Translation :: get('ExternalLink'),1,array ('onclick' => 'javascript:external_link_clicked()'));
		$this->addGroup($choices,null,Translation :: get('applink'),'<br />',false);
		
		$this->addElement('html','<div style="margin-left:25px;display:block;" id="application">');
		$this->addElement('select', MenuItem :: PROPERTY_APPLICATION, Translation :: get('MenuItemApplication'), $this->get_applications());
		$this->addElement('text', MenuItem :: PROPERTY_EXTRA, Translation :: get('MenuItemExtra'));
		$this->addElement('html','</div>');
		
		$this->addElement('html','<div style="margin-left:25px;display:block;" id="external_link">');
		$this->addElement('text', MenuItem :: PROPERTY_URL, Translation :: get('Url'));
		$this->addElement('html','</div>');
		
		$hidden = 'external_link';
		
		if($this->form_type == self :: TYPE_EDIT && $this->menuitem && $this->menuitem->get_application() == '')
		{
			$hidden = 'application';
		}
		
		$this->addElement('html',"<script type=\"text/javascript\">
					/* <![CDATA[ */
					document.getElementById('" . $hidden . "').style.display='none';
					function application_clicked() {
						document.getElementById('application').style.display='';
						document.getElementById('external_link').style.display='none';
					}
					function external_link_clicked() {
						document.getElementById('external_link').style.display='';
						document.getElementById('application').style.display='none';
					}
					/* ]]> */
					</script>\n");
		
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
		
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
    	
    	if($values['app'] == 0)
    	{
    		$menuitem->set_application($values[MenuItem :: PROPERTY_APPLICATION]);
    		$menuitem->set_section($values[MenuItem :: PROPERTY_APPLICATION]);
    		$menuitem->set_url('');
    	}
    	else
    	{
    		$url = $values[MenuItem :: PROPERTY_URL];
    		if(substr($url, 0, 7) != 'http://') $url = 'http://' . $url;
    		
    		$menuitem->set_url($url);
    		$menuitem->set_application('');
    		$menuitem->set_section('');
    	}
    	
    	$menuitem->set_category($values[MenuItem :: PROPERTY_CATEGORY]);
    	$menuitem->set_extra($values[MenuItem :: PROPERTY_EXTRA]);
    	
    	return $menuitem->update();
    }
    
    function create_menu_item()
    {
    	$menuitem = $this->menuitem;
    	$values = $this->exportValues();
    	
    	$menuitem->set_title($values[MenuItem :: PROPERTY_TITLE]);
    	
    	if($values['app'] == 0)
    	{
    		$menuitem->set_application($values[MenuItem :: PROPERTY_APPLICATION]);
    		$menuitem->set_section($values[MenuItem :: PROPERTY_APPLICATION]);
    		$menuitem->set_url('');
    	}
    	else
    	{
    		$url = $values[MenuItem :: PROPERTY_URL];
    		if(substr($url, 0, 7) != 'http://') $url = 'http://' . $url;
    		
    		$menuitem->set_url($url);
    		$menuitem->set_application('');
    		$menuitem->set_section('');
    	}
    	
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
			$item_options[$item->get_id()] = '-- ' . $item->get_title();
		}
		return $item_options;
	}
	
	function get_applications()
	{
		$items = Application :: load_all(false);
		$applications = array();
		$applications['root'] = Translation :: get('Root');
		
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
		if($this->form_type == self :: TYPE_EDIT)
			$defaults['app'] = ($menuitem->get_application() != '')?0:1;
		else
			$defaults['app'] = 0;
		$defaults[MenuItem :: PROPERTY_APPLICATION] = $menuitem->get_application();
		$defaults[MenuItem :: PROPERTY_URL] = $menuitem->get_url();
		$defaults[MenuItem :: PROPERTY_EXTRA] = $menuitem->get_extra();
		parent :: setDefaults($defaults);
	}
	
	function get_menu_item()
	{
		return $this->menuitem;
	}
}
?>