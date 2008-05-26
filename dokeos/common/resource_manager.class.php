<?php

/**
 * Manages resources, ensuring that they are only loaded when necessary.
 * Currently only relevant for JavaScript and CSS files.
 * @author Tim De Pauw
 */
class ResourceManager
{
	private static $instance;
	
	private $resources;
	
	private function __construct()
	{
		$this->resources = array();
	}
	
	function resource_loaded($path)
	{
		return array_key_exists($path, $this->resources);
	}
	
	function get_resource_html($path)
	{
		return ($this->resource_loaded($path)
			? ''
			: $this->_get_resource_html($path));
	}
	
	private function _get_resource_html($path)
	{
		preg_match('/[^.]*$/', $path, $matches);
		$extension = $matches[0];
		switch (strtolower($extension))
		{
			case 'css':
				return '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($path) . '"/>';
			case 'js':
				return '<script type="text/javascript" src="' . htmlspecialchars($path) . '"></script>';
			default:
				die('Unknown resource type: ' . $path);
		}
	}
	
	static function get_instance()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
}

?>