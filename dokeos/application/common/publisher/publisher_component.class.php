<?php
/**
 * @package application.lib.encyclopedia
 */
/**
==============================================================================
 *	This class represents a component of a EncyclopediaPublisher. Its output
 *	is included in the publisher's output.
==============================================================================
 */
abstract class PublisherComponent
{
	/**
	 * The ObjectPublisher instance that created this object.
	 */
	private $parent;

	/**
	 * Constructor.
	 * @param ObjectPublisher $parent The creator of this object.
	 */
	function PublisherComponent($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * Returns the creator of this object.
	 * @return ObjectPublisher The creator.
	 */
	protected function get_parent()
	{
		return $this->parent;
	}

	/**
	 * @see ObjectPublisher::get_user_id()
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
	 * @see ObjectPublisher::get_types()
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
	 * @see ObjectPublisher::get_url()
	 */
	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}

	/**
	 * @see ObjectPublisher::get_parameters()
	 */
	function get_parameters()
	{
		return $this->parent->get_parameters();
	}

	/**
	 * @see ObjectPublisher::get_parameter()
	 */
	function get_parameter($name)
	{
		$parameters = $this->get_parameters();
		return $parameters[$name];
	}

	/**
	 * @see ObjectPublisher::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}
	
	function set_default_learning_object($type, $learning_object)
	{
		$this->parent->set_default_learning_object($type, $learning_object);
	}

	/**
	 * @see ObjectPublisher::get_default_object()
	 */
	function get_default_learning_object($type)
	{
		return $this->parent->get_default_learning_object($type);
	}
	
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->parent->redirect($action, $message, $error_message, $extra_params);
	}
	
	function get_extra_parameters()
	{
		return $this->parent->get_extra_parameters();
	}
	
	function set_extra_parameters($parameters)
	{
		$this->parent->set_extra_parameters($parameters);
	}
}
?>