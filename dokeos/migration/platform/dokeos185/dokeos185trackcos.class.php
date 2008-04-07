<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importtrackcos.class.php';

/**
 * This class presents a Dokeos185 track_c_os
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCOs extends ImportTrackC0s
{
	private static $mgdm;
	
	/**
	 * Dokeos185TrackCOs properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_OS = 'os';
	const PROPERTY_COUNTER = 'counter';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackCOs object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackCOs($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_OS, self :: PROPERTY_COUNTER);
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
	 * Returns the id of this Dokeos185TrackCOs.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the os of this Dokeos185TrackCOs.
	 * @return the os.
	 */
	function get_os()
	{
		return $this->get_default_property(self :: PROPERTY_OS);
	}

	/**
	 * Returns the counter of this Dokeos185TrackCOs.
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
		$tablename = 'track_c_os';
		$classname = 'Dokeos185TrackC0s';
			
		return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name);	
	}
}

?>