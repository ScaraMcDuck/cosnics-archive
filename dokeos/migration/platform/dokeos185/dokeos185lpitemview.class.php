<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 lp_item_view
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
	 * Returns the lp_item_id of this Dokeos185LpItemView.
	 * @return the lp_item_id.
	 */
	function get_lp_item_id()
	{
		return $this->get_default_property(self :: PROPERTY_LP_ITEM_ID);
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
	 * Returns the view_count of this Dokeos185LpItemView.
	 * @return the view_count.
	 */
	function get_view_count()
	{
		return $this->get_default_property(self :: PROPERTY_VIEW_COUNT);
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
	 * Returns the total_time of this Dokeos185LpItemView.
	 * @return the total_time.
	 */
	function get_total_time()
	{
		return $this->get_default_property(self :: PROPERTY_TOTAL_TIME);
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
	 * Returns the status of this Dokeos185LpItemView.
	 * @return the status.
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
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
	 * Returns the lesson_location of this Dokeos185LpItemView.
	 * @return the lesson_location.
	 */
	function get_lesson_location()
	{
		return $this->get_default_property(self :: PROPERTY_LESSON_LOCATION);
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
	 * Returns the max_score of this Dokeos185LpItemView.
	 * @return the max_score.
	 */
	function get_max_score()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_SCORE);
	}


}

?>