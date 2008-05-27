<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/home_block.class.php';

class HomeBlockForm extends FormValidator {
	
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';
	
	private $homeblock;
	private $form_type;

    function HomeBlockForm($form_type, $homeblock, $action) {
    	parent :: __construct('home_block', 'post', $action);
    	
    	$this->homeblock = $homeblock;
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
		$this->addElement('text', HomeBlock :: PROPERTY_TITLE, Translation :: get('HomeBlockTitle'));
		$this->addRule(HomeBlock :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('select', HomeBlock :: PROPERTY_COLUMN, Translation :: get('HomeBlockColumn'), $this->get_columns());
		$this->addRule(HomeBlock :: PROPERTY_COLUMN, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('select', HomeBlock :: PROPERTY_COMPONENT, Translation :: get('HomeBlockComponent'), $this->get_application_components());
		$this->addRule(HomeBlock :: PROPERTY_COMPONENT, Translation :: get('ThisFieldIsRequired'), 'required');
		
//		$contains_blocks = array();
//		$contains_blocks[] =& $this->createElement('radio', null, null, Translation :: get('Yes'), 1);
//		$contains_blocks[] =& $this->createElement('radio', null, null, Translation :: get('No'), 0);
//		$this->addGroup($contains_blocks, HomeBlock :: PROPERTY_CONTAINS_BLOCKS, Translation :: get('HomeBlockContainsBlocks'), '<br />');
				
		$this->addElement('submit', 'home_block', Translation :: get('Ok'));
    }
    
    function build_editing_form()
    {
	   	$this->build_basic_form();
    	$this->addElement('hidden', HomeBlock :: PROPERTY_ID );
    }
    
    function build_creation_form()
    {
    	$this->build_basic_form();
    }
    
    function update_object()
    {
    	$homeblock = $this->homeblock;
    	$values = $this->exportValues();
    	
    	$homeblock->set_title($values[HomeBlock :: PROPERTY_TITLE]);
    	$homeblock->set_column($values[HomeBlock :: PROPERTY_COLUMN]);
    	$homeblock->set_component($values[HomeBlock :: PROPERTY_COMPONENT]);
//    	$homeblock->set_contains_blocks($values[HomeBlock :: PROPERTY_CONTAINS_BLOCKS]);
    	
    	return $homeblock->update();
    }
    
    function create_object()
    {
    	$homeblock = $this->homeblock;
    	$values = $this->exportValues();
    	$failures = 0;
    	
    	$homeblock->set_title($values[HomeBlock :: PROPERTY_TITLE]);
    	$homeblock->set_column($values[HomeBlock :: PROPERTY_COLUMN]);
    	$homeblock->set_component($values[HomeBlock :: PROPERTY_COMPONENT]);
//    	$homeblock->set_contains_blocks($values[HomeBlock :: PROPERTY_CONTAINS_BLOCKS]);
    	
    	if (!$homeblock->create())
    	{
    		$failures++;
    	}
    	
    	$homeblockconfigs = HomeDataManager :: get_instance()->retrieve_block_properties($values[HomeBlock :: PROPERTY_COMPONENT]);
    	
    	foreach ($homeblockconfigs as $variable => $value)
    	{
    		$homeblockconfig = new HomeBlockConfig($homeblock->get_id());
    		{
    			$homeblockconfig->set_variable($variable);
    			$homeblockconfig->set_value($value);
    			
    			if (!$homeblockconfig->create())
    			{
    				$failures++;
    			}
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
    
	function get_columns()
	{
		$columns = HomeDataManager :: get_instance()->retrieve_home_columns();
		$column_options = array();
		while ($column = $columns->next_result())
		{
			$column_options[$column->get_id()] = $column->get_title(); 
		}
		
		return $column_options;
	}
	
	function get_application_components()
	{
		$application_components = array();
		$applications = HomeDataManager :: get_instance()->get_applications();
		
		foreach ($applications as $application)
		{
			$path = dirname(__FILE__).'/../../application/lib/'.$application.'/block';
			if ($handle = opendir($path))
			{
				while (false !== ($file = readdir($handle)))
				{
					if (!is_dir($file) && stripos($file, '.class.php') !== false)
					{
						$component = str_replace('.class.php', '', $file);
						$component = str_replace(strtolower(Application :: application_to_class($application)), '', $component);
						
						$item = Application :: application_to_class($application) . '.' . ucfirst($component);
						$display = Translation :: get(Application :: application_to_class($application)) . '&nbsp;>&nbsp;' . ucfirst($component);
						$application_components[$item] = $display;
					}
				}
				closedir($handle);
			}
		}
		
		$application_components['User'] = 'User';
		asort($application_components);
		
		return $application_components;
	}
    
	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$homeblock = $this->homeblock;
		$defaults[HomeBlock :: PROPERTY_ID] = $homeblock->get_id();
		$defaults[HomeBlock :: PROPERTY_TITLE] = $homeblock->get_title();
		$defaults[HomeBlock :: PROPERTY_COLUMN] = $homeblock->get_column();
		$defaults[HomeBlock :: PROPERTY_COMPONENT] = $homeblock->get_component();
//		$defaults[HomeBlock :: PROPERTY_CONTAINS_BLOCKS] = $homeblock->get_contains_blocks();
		parent :: setDefaults($defaults);
	}
}
?>