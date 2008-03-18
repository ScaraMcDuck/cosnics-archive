<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 lp_view
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpView
{
	/**
	 * Dokeos185LpView properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LP_ID = 'lp_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_VIEW_COUNT = 'view_count';
	const PROPERTY_LAST_ITEM = 'last_item';
	const PROPERTY_PROGRESS = 'progress';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185LpView object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185LpView($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_LP_ID, SELF :: PROPERTY_USER_ID, SELF :: PROPERTY_VIEW_COUNT, SELF :: PROPERTY_LAST_ITEM, SELF :: PROPERTY_PROGRESS);
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
	 * Returns the id of this Dokeos185LpView.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the lp_id of this Dokeos185LpView.
	 * @return the lp_id.
	 */
	function get_lp_id()
	{
		return $this->get_default_property(self :: PROPERTY_LP_ID);
	}

	/**
	 * Returns the user_id of this Dokeos185LpView.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Returns the view_count of this Dokeos185LpView.
	 * @return the view_count.
	 */
	function get_view_count()
	{
		return $this->get_default_property(self :: PROPERTY_VIEW_COUNT);
	}

	/**
	 * Returns the last_item of this Dokeos185LpView.
	 * @return the last_item.
	 */
	function get_last_item()
	{
		return $this->get_default_property(self :: PROPERTY_LAST_ITEM);
	}

	/**
	 * Returns the progress of this Dokeos185LpView.
	 * @return the progress.
	 */
	function get_progress()
	{
		return $this->get_default_property(self :: PROPERTY_PROGRESS);
	}


}

?>