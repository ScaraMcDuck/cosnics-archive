<?php
/**
 * $Id$
 * @package application
 */
require_once dirname(__FILE__).'/application.class.php';

abstract class WebApplication extends Application {

	private $parameters;

	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_APPLICATION = 'application';

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
	function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
	{
		if (count($parameters))
		{
			$parameters = array_merge($this->parameters, $parameters);
		}
		else
		{
			$parameters = $this->parameters;
		}

		if ($filter)
		{
			foreach ($parameters as $key => $value)
			{
				if (!in_array($key, $filterOn))
				{
					$url_parameters[$key] = $value;
				}
			}
		}

		$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'].'?'.http_build_query(($filter ? $url_parameters : $parameters));
		if ($encode)
		{
			$url = htmlentities($url);
		} 
		return $url;
	}

	/**
	 * Redirect the end user to another location.
	 * @param string $action The action to take (default = browse learning
	 * objects).
	 * @param string $message The message to show (default = no message).
	 * @param int $new_category_id The category to show (default = root
	 * category).
	 * @param boolean $error_message Is the passed message an error message?
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		$params = array ();

		if (isset($extra_params))
		{
			foreach($extra_params as $key => $extra)
			{
				$params[$key] = $extra;
			}
		}

		if (isset ($message))
		{
			$params[$error_message ? self :: PARAM_ERROR_MESSAGE :  self :: PARAM_MESSAGE] = $message;
		}
		
		$url = $this->get_url($params);
		header('Location: '.$url); 
		
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