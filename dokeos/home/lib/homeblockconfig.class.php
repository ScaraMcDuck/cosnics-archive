<?php
require_once dirname(__FILE__).'/homedatamanager.class.php';

class HomeBlockConfig {

	const PROPERTY_BLOCK_ID = 'block_id';
	const PROPERTY_VARIABLE = 'variable';
	const PROPERTY_VALUE = 'value';
	
	private $block_id;
	private $defaultProperties;

    function HomeBlockConfig($block_id = null, $defaultProperties = array ())
    {
    	$this->block_id = $block_id;
		$this->defaultProperties = $defaultProperties;
    }
    
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Get the default properties of all user course categories.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_BLOCK_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_VALUE);
	}
	
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
    
    function get_block_id()
    {
    	return $this->block_id;
    }
    
    function set_block_id($block_id)
	{
		$this->block_id = $block_id;
	}
	
    function get_variable()
    {
    	return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }
    
	function set_variable($variable)
	{
		$this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
	}
	
    function get_value()
    {
    	return $this->get_default_property(self :: PROPERTY_VALUE);
    }
    
	function set_value($value)
	{
		$this->set_default_property(self :: PROPERTY_VALUE, $value);
	}
	
	function update()
	{
		$wdm = HomeDataManager :: get_instance();
		$success = $wdm->update_home_block_config($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function create()
	{
		$wdm = HomeDataManager :: get_instance();
		$success = $wdm->create_home_block_config($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function delete()
	{
		$wdm = HomeDataManager :: get_instance();
		$success = $wdm->delete_home_block_config($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
}
?>