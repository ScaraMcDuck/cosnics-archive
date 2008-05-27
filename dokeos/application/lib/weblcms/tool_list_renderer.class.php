<?php
/**
 * $Id$
 * @package application.weblcms
 */
/**
 * Renderer to display a set of tools
 */
abstract class ToolListRenderer
{
	/**
	 * The parent application
	 */
	private $parent;
	/**
	 * Constructor
	 * @param WebLcms $parent The parent application
	 */
	function ToolListRenderer($parent)
	{
		$this->parent = $parent;
	}
	/**
	 * Create a new tool list renderer
	 * @param string $class The implementation of this abstract class to load
	 * @param WebLcms $parent The parent application
	 */
	static function factory($class,$parent)
	{
		$class .= 'ToolListRenderer';
		require_once(dirname(__FILE__).'/tool_list_renderer/'.strtolower($class).'.class.php');
		return new $class($parent);
	}
	/**
	 * Gets the parent application
	 * @return WebLcms
	 */
	function get_parent()
	{
		return $this->parent;
	}
	/**
	 * Displays the tool list.
	 */
	abstract function display();
}
?>