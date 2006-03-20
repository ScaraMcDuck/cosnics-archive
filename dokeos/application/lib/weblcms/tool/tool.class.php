<?php
abstract class Tool
{
	private $parameters;
	
	function Tool()
	{
		$this->parameters = array();
	}
	
	abstract function run();
	
	function get_parameters()
	{
		return $this->parameters;
	}
	
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}

	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	function get_url($parameters = array())
	{
		$string = '';
		if (count($parameters))
		{
			$parameters = array_merge($this->parameters, $parameters);
		}
		else
		{
			$parameters = & $this->parameters;
		}
		foreach ($parameters as $name => $value)
		{
			$string .= '&' . urlencode($name) . '=' . urlencode($value);
		}
		return $_SERVER['PHP_SELF'] . '?' . $string;
	}
}
?>