<?php
abstract class Tool
{
	private $parent;
	
	function Tool($parent)
	{
		$this->parent = $parent;
	}
	
	abstract function run();
	
	function get_parent()
	{
		return $this->parent;
	}
	
	function get_parameters()
	{
		return $this->parent->get_parameters();
	}
	
	function get_parameter($name)
	{
		return $this->parent->get_parameter($name);
	}

	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}

	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}
}
?>