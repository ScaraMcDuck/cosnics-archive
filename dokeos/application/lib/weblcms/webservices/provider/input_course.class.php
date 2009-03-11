<?php

class InputCourse
{
	const PROPERTY_ID = 'id';

    const PROPERTY_USER_ID = 'user_id';

    const PROPERTY_TOOL = 'tool';
	
	private $defaultProperties;

	function InputUser($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID,self :: PROPERTY_USER_ID,self :: PROPERTY_TOOL);
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

    function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

    function get_tool()
	{
		return $this->get_default_property(self :: PROPERTY_TOOL);
	}
	
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

    function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}

    function set_tool($tool)
	{
		$this->set_default_property(self :: PROPERTY_TOOL, $tool);
	}
}
?>