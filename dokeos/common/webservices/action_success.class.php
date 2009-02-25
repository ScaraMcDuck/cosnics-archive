<?php
/*This is used for passing along a "succeed" boolean, 
 * after an update/delete/create webservice is called.
 * This is necessary because the existing framework only passes
 * objects with properties back and forth, so using 'new Boolean()' and returning
 * true, or something like that, doesn't work.
 */
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