<?php
/**
 * @package application.personal_calendar
 */
abstract class PersonalCalendarPublisherComponent
{
	/**
	 * The PersonalCalendarPublisher instance that created this object.
	 */
	private $parent;
	/**
	 * Creates a new publisher component
	 * @param PersonalCalendarPublisher $parent
	 */
	function PersonalCalendarPublisherComponent($parent)
	{
		$this->parent = $parent;
	}
	/**
	 * Returns the publisher component's output in HTML format.
	 * @return string The output.
	 */
	abstract function as_html();
	/**
	 * @see PersonalCalendarPublisher::get_url()
	 */
	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}
	/**
	 * @see PersonalCalendarPublisher::get_user_id()
	 */
	function get_user_id()
	{
		return $this->parent->get_user_id();
	}
}
?>