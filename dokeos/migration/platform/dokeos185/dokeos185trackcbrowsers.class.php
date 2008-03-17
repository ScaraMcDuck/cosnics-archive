<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 track_c_browsers
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCBrowsers
{
	/**
	 * Dokeos185TrackCBrowsers properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_BROWSER = 'browser';
	const PROPERTY_COUNTER = 'counter';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackCBrowsers object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackCBrowsers($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_BROWSER, SELF :: PROPERTY_COUNTER);
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
	 * Returns the id of this Dokeos185TrackCBrowsers.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185TrackCBrowsers.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the browser of this Dokeos185TrackCBrowsers.
	 * @return the browser.
	 */
	function get_browser()
	{
		return $this->get_default_property(self :: PROPERTY_BROWSER);
	}

	/**
	 * Sets the browser of this Dokeos185TrackCBrowsers.
	 * @param browser
	 */
	function set_browser($browser)
	{
		$this->set_default_property(self :: PROPERTY_BROWSER, $browser);
	}
	/**
	 * Returns the counter of this Dokeos185TrackCBrowsers.
	 * @return the counter.
	 */
	function get_counter()
	{
		return $this->get_default_property(self :: PROPERTY_COUNTER);
	}

	/**
	 * Sets the counter of this Dokeos185TrackCBrowsers.
	 * @param counter
	 */
	function set_counter($counter)
	{
		$this->set_default_property(self :: PROPERTY_COUNTER, $counter);
	}

}

?>