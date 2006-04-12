<?php

/**
==============================================================================
 *	This is the base class for all tools used in applications.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class Tool
{
	/**
	 * The application that the tool is associated with.
	 */
	private $parent;
	
	/**
	 * Constructor.
	 * @param Application $parent The application that the tool is associated
	 *                            with.
	 */ 
	function Tool($parent)
	{
		$this->parent = $parent;
	}
	
	/**
	 * Runs the tool, performing whatever actions are necessary.
	 */
	abstract function run();
	
	/**
	 * Returns the application that this tool is associated with.
	 * @return Application The application.
	 */
	function get_parent()
	{
		return $this->parent;
	}
	
	/**
	 * @see Application :: get_tool_id()
	 */
	function get_tool_id()
	{
		return $this->parent->get_tool_id();
	}
	
	/**
	 * @see Application :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->parent->get_parameters();
	}
	
	/**
	 * @see Application :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->parent->get_parameter($name);
	}

	/**
	 * @see Application :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}

	/**
	 * @see Application :: get_url()
	 */
	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}
}
?>