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
	function get_parent()
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
		return $this->get_parent()->get_url($parameters, $encode);
	}

	/**
	 * @see ObjectPublisher::get_parameter()
	 */
	function get_parameter($name)
	{
		$this->get_parent()->get_parameter($name);
	}

	/**
	 * @see ObjectPublisher::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}
	
	function set_default_learning_object($type, $learning_object)
	{
		$this->get_parent()->set_default_learning_object($type, $learning_object);
	}

	/**
	 * @see ObjectPublisher::get_default_object()
	 */
	function get_default_learning_object($type)
	{
		return $this->get_parent()->get_default_learning_object($type);
	}
	
	function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
	{
		return $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
	}
	
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	function set_parameters($parameters)
	{
		$this->get_parent()->set_parameters($parameters);
	}
}
?>