<?php
/**
 * @package application.lib.calendar
 */
abstract class PersonalCalendarBlockComponent
{
	/**
	 * The ObjectPublisher instance that created this object.
	 */
	private $parent;

	/**
	 * Constructor.
	 * @param ObjectPublisher $parent The creator of this object.
	 */
	function PersonalCalendarBlockComponent($parent)
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
	
	protected function get_configuration()
	{
		return $this->parent->get_configuration();
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
	protected function get_type()
	{
		return $this->parent->get_type();
	}
	
	function get_block_info()
	{
		return $this->parent->get_block_info();
	}
	
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}

	/**
	 * Returns the publisher component's output in HTML format.
	 * @return string The output.
	 */
	abstract function run();

	/**
	 * @see ObjectPublisher::get_url()
	 */
	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}
	
	function get_link($parameters = array(), $encode = false)
	{
		return $this->parent->get_link($parameters, $encode);
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
	
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->parent->redirect($action, $message, $error_message, $extra_params);
	}
	
	function get_events($from_date,$to_date)
	{
		return $this->parent->get_events($from_date,$to_date);
	}
	
	function retrieve_calendar_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->parent->retrieve_calendar_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	function get_publication_viewing_link($calendar)
	{
		return $this->parent->get_publication_viewing_link($calendar);
	}
}
?>