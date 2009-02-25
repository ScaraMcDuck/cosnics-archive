<?php

class ActionSuccess
{
	const PROPERTY_SUCCESS = 'success';
	
	private $defaultProperties;

	function ActionSuccess($defaultProperties = array ())
	{
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
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_SUCCESS);
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	function get_success()
	{
		return $this->get_default_property(self :: PROPERTY_SUCCESS);
	}
	
	function set_success($success)
	{
		$this->set_default_property(self :: PROPERTY_SUCCESS, $success);
	}	
	
	function to_array()
	{
		return $this->defaultProperties;
	}
}
?>