<?php

/**
==============================================================================
 *	This class represents a component of a LearningObjectPublisher. Its output
 *	is included in the publisher's output.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class LearningObjectPublisherComponent
{
	/**
	 * The LearningObjectPublisher instance that created this object.
	 */
	private $parent;

	/**
	 * Constructor.
	 * @param LearningObjectPublisher $parent The creator of this object.
	 */
	function LearningObjectPublisherComponent($parent)
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

	/**
	 * @see LearningObjectPublisher::get_course_id()
	 */
	protected function get_course_id()
	{
		return $this->parent->get_course_id();
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
	 * @see LearningObjectPublisher::get_categories()
	 */
	function get_categories()
	{
		return $this->parent->get_categories();
	}
}
?>