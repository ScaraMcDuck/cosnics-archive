<?php
/**
 * $Id: configuration.class.php 14277 2008-02-08 21:13:49Z Scara84 $
 * @package repository
 */

/**
 *	This class represents the current configuration.
 *
 *	@author Tim De Pauw
 */

class Configuration
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Parameters defined in the configuration. Stored as an associative array.
	 */
	private $params;

	/**
	 * Constructor.
	 */
	private function Configuration()
	{
		global $configuration;
		require_once dirname(__FILE__).'/configuration.php';
		$this->params = $configuration;
	}

	/**
	 * Returns the instance of this class.
	 * @return Configuration The instance.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			self :: $instance = new self();
		}
		return self :: $instance;
	}

	/**
	 * Gets a parameter from the configuration.
	 * @param string $section The name of the section in which the parameter
	 *                        is located.
	 * @param string $name The parameter name.
	 * @return mixed The parameter value.
	 */
	function get_parameter($section, $name)
	{
		return $this->params[$section][$name];
	}
}
?>