<?php
require_once dirname(__FILE__).'/application.class.php';

abstract class WebApplication extends Application {
	private $parameters;
	
	function WebApplication()
	{
		$this->parameters = array();
	}
	
	/**
	 * Gets the URL of the current page in the application. Optionally takes
	 * an associative array of name/value pairs representing additional query
	 * string parameters; these will either be added to the parameters already
	 * present, or override them if a value with the same name exists.
	 * @param array $parameters The additional parameters, or null if none.
	 * @param boolean $encode Whether or not to encode HTML entities. Defaults
	 *                        to false.
	 * @return string The URL.
	 */
	function get_url($parameters = array (), $encode = false)
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
		$pairs = array ();
		foreach ($parameters as $name => $value)
		{
			$pairs[] = urlencode($name).'='.urlencode($value);
		}
		$url = $_SERVER['PHP_SELF'].'?'.join('&', $pairs);
		if ($encode)
		{
			$url = htmlentities($url);
		}
		return $url;
	}
	
	/**
	 * Returns the current URL parameters.
	 * @return array The parameters.
	 */
	function get_parameters()
	{
		return $this->parameters;
	}
	
	/**
	 * Returns the value of the given URL parameter.
	 * @param string $name The parameter name.
	 * @return string The parameter value.
	 */
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}
	
	/**
	 * Sets the value of a URL parameter.
	 * @param string $name The parameter name.
	 * @param string $value The parameter value.
	 */
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}
}
?>