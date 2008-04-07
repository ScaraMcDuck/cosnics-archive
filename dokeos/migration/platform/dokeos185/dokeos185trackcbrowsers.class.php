<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importtrackcbrowsers.class.php';

/**
 * This class presents a Dokeos185 track_c_browsers
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCBrowsers extends ImportTrackCBrowsers
{
	private static $mgdm;
	
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_BROWSER, self :: PROPERTY_COUNTER);
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
	 * Returns the browser of this Dokeos185TrackCBrowsers.
	 * @return the browser.
	 */
	function get_browser()
	{
		return $this->get_default_property(self :: PROPERTY_BROWSER);
	}

	/**
	 * Returns the counter of this Dokeos185TrackCBrowsers.
	 * @return the counter.
	 */
	function get_counter()
	{
		return $this->get_default_property(self :: PROPERTY_COUNTER);
	}

	function is_valid($array)
	{
		$course = $array['course'];
	}
	
	function convert_to_lcms($array)
	{	
		$course = $array['course'];
	}
	
	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];
		
		$db = 'statistics_database';
		$tablename = 'track_c_browsers';
		$classname = 'Dokeos185TrackCBrowsers';
			
		return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name);	
	}

}

?>