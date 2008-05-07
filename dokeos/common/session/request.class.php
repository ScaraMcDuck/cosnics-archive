<?php
class Request
{
	function get($variable)
	{
		$value = $_GET[$variable];
		// TODO: Add the necessary security filters if and where necessary
		$value = Security :: remove_XSS($value);
		return $value;
	}
	
	function post($variable)
	{
		$value = $_POST[$variable];
		// TODO: Add the necessary security filters if and where necessary
		return $value;
	}
	
	function server($variable)
	{
		$value = $_SERVER[$variable];
		// TODO: Add the necessary security filters if and where necessary
		return $value;
	}
	
	function file($variable)
	{
		$value = $_FILES[$variable];
		// TODO: Add the necessary security filters if and where necessary
		return $value;
	}
	
	function environment($variable)
	{
		$value = $_ENV[$variable];
		// TODO: Add the necessary security filters if and where necessary
		return $value;
	}
}
?>