<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/home_block.class.php';
require_once dirname(__FILE__).'/home_block_config.class.php';

class HomeBlockConfigForm extends FormValidator {
	
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';
	
	private $homeblock;
	private $homeblock_config;

    function HomeBlockConfigForm($homeblock, $action) {
    	parent :: __construct('home_block', 'post', $action);
    	
    	$this->homeblock = $homeblock;
    	$this->homeblock_config = $homeblock->get_configuration();
		$this->build_form();
		$this->setDefaults();
    }
    
    function build_form()
    {
		$homeblock_config = $this->homeblock_config;
		foreach ($homeblock_config as $key => $value)
		{
			$this->addElement('text', $key, $key);
			$this->addRule($key, Translation :: get('ThisFieldIsRequired'), 'required');
		}
				
		$this->addElement('submit', 'home_block', Translation :: get('Ok'));
    	$this->addElement('hidden', HomeBlock :: PROPERTY_ID );
    }
    
    function update_block_config()
    {
    	$homeblock = $this->homeblock;
    	$values = $this->exportValues();
    	$failures = 0;
    	
		$homeblock_config = $this->homeblock_config;
		foreach ($homeblock_config as $key => $value)
		{
			$block_config = new HomeBlockConfig();
			$block_config->set_block_id($homeblock->get_id());
			$block_config->set_variable($key);
			$block_config->set_value($values[$key]);
			
			if (!$block_config->update())
			{
				return false;
			}
		}
    	
    	return true;
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
		
		$homeblock_config = $this->homeblock_config;
		foreach ($homeblock_config as $key => $value)
		{
			$defaults[$key] = $value;
		}
		
		parent :: setDefaults($defaults);
	}
}
?>