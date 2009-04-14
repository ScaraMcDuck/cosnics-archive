<?php
/**
 * @package application.lib.portfolio
 */
/**
==============================================================================
 *	This class represents a component of a PortfolioPublisher. Its output
 *	is included in the publisher's output.
==============================================================================
 */
abstract class PortfolioPublisherComponent
{
	/**
	 * The LearningObjectPublisher instance that created this object.
	 */
	private $parent;

	/**
	 * Constructor.
	 * @param LearningObjectPublisher $parent The creator of this object.
	 */
	function PortfolioPublisherComponent($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * Returns the creator of this object.
	 * @return LearningObjectPublisher The creator.
	 */
	protected function get_parent()
	{
		return $this->parent;
	}

	/**
	 * @see LearningObjectPublisher::get_user_id()
	 */
	protected function get_user_id()
	{
		return $this->parent->get_user_id();
	}
	
	function get_user()
	{
		return $this->parent->get_user();
	}

	/**
	 * @see LearningObjectPublisher::get_types()
	 */
	protected function get_types()
	{
		return $this->parent->get_types();
	}

	/**
	 * Returns the publisher component's output in HTML format.
	 * @return string The output.
	 */
	abstract function as_html();

	/**
	 * @see LearningObjectPublisher::get_url()
	 */
	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}

	/**
	 * @see LearningObjectPublisher::get_parameters()
	 */
	function get_parameters()
	{
		return $this->parent->get_parameters();
	}

	/**
	 * @see LearningObjectPublisher::get_parameter()
	 */
	function get_parameter($name)
	{
		$parameters = $this->get_parameters();
		return $parameters[$name];
	}

	/**
	 * @see LearningObjectPublisher::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}

	/**
	 * @see LearningObjectPublisher::get_default_learning_object()
	 */
	function get_default_learning_object($type)
	{
		return $this->parent->get_default_learning_object($type);
	}
	
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->parent->redirect($action, $message, $error_message, $extra_params);
	}
	
	function get_path($path_type)
	{
		return $this->parent->get_path($path_type);
	}
}
?>