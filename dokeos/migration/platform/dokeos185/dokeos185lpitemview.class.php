<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 lp_item_view
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpItemView
{
	/**
	 * Dokeos185LpItemView properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LP_ITEM_ID = 'lp_item_id';
	const PROPERTY_LP_VIEW_ID = 'lp_view_id';
	const PROPERTY_VIEW_COUNT = 'view_count';
	const PROPERTY_START_TIME = 'start_time';
	const PROPERTY_TOTAL_TIME = 'total_time';
	const PROPERTY_SCORE = 'score';
	const PROPERTY_STATUS = 'status';
	const PROPERTY_SUSPEND_DATA = 'suspend_data';
	const PROPERTY_LESSON_LOCATION = 'lesson_location';
	const PROPERTY_CORE_EXIT = 'core_exit';
	const PROPERTY_MAX_SCORE = 'max_score';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185LpItemView object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185LpItemView($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_LP_ITEM_ID, SELF :: PROPERTY_LP_VIEW_ID, SELF :: PROPERTY_VIEW_COUNT, SELF :: PROPERTY_START_TIME, SELF :: PROPERTY_TOTAL_TIME, SELF :: PROPERTY_SCORE, SELF :: PROPERTY_STATUS, SELF :: PROPERTY_SUSPEND_DATA, SELF :: PROPERTY_LESSON_LOCATION, SELF :: PROPERTY_CORE_EXIT, SELF :: PROPERTY_MAX_SCORE);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the id of this Dokeos185LpItemView.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185LpItemView.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the lp_item_id of this Dokeos185LpItemView.
	 * @return the lp_item_id.
	 */
	function get_lp_item_id()
	{
		return $this->get_default_property(self :: PROPERTY_LP_ITEM_ID);
	}

	/**
	 * Sets the lp_item_id of this Dokeos185LpItemView.
	 * @param lp_item_id
	 */
	function set_lp_item_id($lp_item_id)
	{
		$this->set_default_property(self :: PROPERTY_LP_ITEM_ID, $lp_item_id);
	}
	/**
	 * Returns the lp_view_id of this Dokeos185LpItemView.
	 * @return the lp_view_id.
	 */
	function get_lp_view_id()
	{
		return $this->get_default_property(self :: PROPERTY_LP_VIEW_ID);
	}

	/**
	 * Sets the lp_view_id of this Dokeos185LpItemView.
	 * @param lp_view_id
	 */
	function set_lp_view_id($lp_view_id)
	{
		$this->set_default_property(self :: PROPERTY_LP_VIEW_ID, $lp_view_id);
	}
	/**
	 * Returns the view_count of this Dokeos185LpItemView.
	 * @return the view_count.
	 */
	function get_view_count()
	{
		return $this->get_default_property(self :: PROPERTY_VIEW_COUNT);
	}

	/**
	 * Sets the view_count of this Dokeos185LpItemView.
	 * @param view_count
	 */
	function set_view_count($view_count)
	{
		$this->set_default_property(self :: PROPERTY_VIEW_COUNT, $view_count);
	}
	/**
	 * Returns the start_time of this Dokeos185LpItemView.
	 * @return the start_time.
	 */
	function get_start_time()
	{
		return $this->get_default_property(self :: PROPERTY_START_TIME);
	}

	/**
	 * Sets the start_time of this Dokeos185LpItemView.
	 * @param start_time
	 */
	function set_start_time($start_time)
	{
		$this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
	}
	/**
	 * Returns the total_time of this Dokeos185LpItemView.
	 * @return the total_time.
	 */
	function get_total_time()
	{
		return $this->get_default_property(self :: PROPERTY_TOTAL_TIME);
	}

	/**
	 * Sets the total_time of this Dokeos185LpItemView.
	 * @param total_time
	 */
	function set_total_time($total_time)
	{
		$this->set_default_property(self :: PROPERTY_TOTAL_TIME, $total_time);
	}
	/**
	 * Returns the score of this Dokeos185LpItemView.
	 * @return the score.
	 */
	function get_score()
	{
		return $this->get_default_property(self :: PROPERTY_SCORE);
	}

	/**
	 * Sets the score of this Dokeos185LpItemView.
	 * @param score
	 */
	function set_score($score)
	{
		$this->set_default_property(self :: PROPERTY_SCORE, $score);
	}
	/**
	 * Returns the status of this Dokeos185LpItemView.
	 * @return the status.
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}

	/**
	 * Sets the status of this Dokeos185LpItemView.
	 * @param status
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}
	/**
	 * Returns the suspend_data of this Dokeos185LpItemView.
	 * @return the suspend_data.
	 */
	function get_suspend_data()
	{
		return $this->get_default_property(self :: PROPERTY_SUSPEND_DATA);
	}

	/**
	 * Sets the suspend_data of this Dokeos185LpItemView.
	 * @param suspend_data
	 */
	function set_suspend_data($suspend_data)
	{
		$this->set_default_property(self :: PROPERTY_SUSPEND_DATA, $suspend_data);
	}
	/**
	 * Returns the lesson_location of this Dokeos185LpItemView.
	 * @return the lesson_location.
	 */
	function get_lesson_location()
	{
		return $this->get_default_property(self :: PROPERTY_LESSON_LOCATION);
	}

	/**
	 * Sets the lesson_location of this Dokeos185LpItemView.
	 * @param lesson_location
	 */
	function set_lesson_location($lesson_location)
	{
		$this->set_default_property(self :: PROPERTY_LESSON_LOCATION, $lesson_location);
	}
	/**
	 * Returns the core_exit of this Dokeos185LpItemView.
	 * @return the core_exit.
	 */
	function get_core_exit()
	{
		return $this->get_default_property(self :: PROPERTY_CORE_EXIT);
	}

	/**
	 * Sets the core_exit of this Dokeos185LpItemView.
	 * @param core_exit
	 */
	function set_core_exit($core_exit)
	{
		$this->set_default_property(self :: PROPERTY_CORE_EXIT, $core_exit);
	}
	/**
	 * Returns the max_score of this Dokeos185LpItemView.
	 * @return the max_score.
	 */
	function get_max_score()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_SCORE);
	}

	/**
	 * Sets the max_score of this Dokeos185LpItemView.
	 * @param max_score
	 */
	function set_max_score($max_score)
	{
		$this->set_default_property(self :: PROPERTY_MAX_SCORE, $max_score);
	}

}

?>