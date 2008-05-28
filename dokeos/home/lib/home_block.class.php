<?php
require_once dirname(__FILE__).'/home_data_manager.class.php';

class HomeBlock {

	const PROPERTY_ID = 'id';
	const PROPERTY_COLUMN = 'column';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_APPLICATION = 'application';
	const PROPERTY_COMPONENT = 'component';
	
	private $id;
	private $defaultProperties;

    function HomeBlock($id = null, $defaultProperties = array ())
    {
    	$this->id = $id;
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_COLUMN, self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_APPLICATION, self :: PROPERTY_COMPONENT);
	}
	
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
    
    function get_id()
    {
    	return $this->id;
    }
    
    function set_id($id)
	{
		$this->id = $id;
	}
	
    function get_sort()
    {
    	return $this->get_default_property(self :: PROPERTY_SORT);
    }
    
	function set_sort($sort)
	{
		$this->set_default_property(self :: PROPERTY_SORT, $sort);
	}
    
    function get_column()
    {
    	return $this->get_default_property(self :: PROPERTY_COLUMN);
    }
	
	function set_column($column)
	{
		$this->set_default_property(self :: PROPERTY_COLUMN, $column);
	}
    
    function get_title()
    {
    	return $this->get_default_property(self :: PROPERTY_TITLE);
    }
	
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	
    function get_application()
    {
    	return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }
	
	function set_application($application)
	{
		$this->set_default_property(self :: PROPERTY_APPLICATION, $application);
	}
	
    function get_component()
    {
    	return $this->get_default_property(self :: PROPERTY_COMPONENT);
    }
	
	function set_component($component)
	{
		$this->set_default_property(self :: PROPERTY_COMPONENT, $component);
	}
	
	function update()
	{
		$wdm = HomeDataManager :: get_instance();
		$success = $wdm->update_home_block($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function create()
	{
		$wdm = HomeDataManager :: get_instance();
		$id = $wdm->get_next_home_block_id();
		$this->set_id($id);
		$success_block = $wdm->create_home_block($this);
		if (!$success_block)
		{
			return false;
		}

		return true;
	}
	
	function delete()
	{
		$hdm = HomeDataManager :: get_instance();
		$success_config = $hdm->delete_home_block_configs($this);
		$success_block = $hdm->delete_home_block($this);
		
		if (!$success_block || !$success_config)
		{
			return false;
		}

		return true;
	}
	
	function get_configuration()
	{		
		$hdm = HomeDataManager :: get_instance();
		$condition = new EqualityCondition(HomeBlockConfig :: PROPERTY_BLOCK_ID, $this->get_id());
		$configs = $hdm->retrieve_home_block_config($condition);
		$configuration = array();
		
		while ($config = $configs->next_result())
		{
			$configuration[$config->get_variable()] = $config->get_value();
		}
		return $configuration;
	}
	
	function is_configurable()
	{
		$hdm = HomeDataManager :: get_instance();
		$condition = new EqualityCondition(HomeBlockConfig :: PROPERTY_BLOCK_ID, $this->get_id());
		$count = $hdm->count_home_block_config($condition);
		return ($count > 0);
	}
}
?>