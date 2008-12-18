<?php

class OutputUser
{
	const PROPERTY_NAME = 'name';
	const PROPERTY_EMAIL = 'email';
	
	private $defaultProperties;

	function OutputUser($defaultProperties = array ())
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
		return array (self :: PROPERTY_NAME, self :: PROPERTY_EMAIL);
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}	
	
	function get_email()
	{
		return $this->get_default_property(self :: PROPERTY_EMAIL);
	}
	
	function set_email($email)
	{
		$this->set_default_property(self :: PROPERTY_EMAIL, $email);
	}	
	
	function to_array()
	{
		return $this->defaultProperties;
	}
}
?>