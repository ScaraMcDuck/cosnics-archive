<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importtrackeonline.class.php';

/**
 * This class presents a Dokeos185 track_e_online
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEOnline extends ImportTrackEOnline
{
	private static $mgdm;
	
	/**
	 * Dokeos185TrackEOnline properties
	 */
	const PROPERTY_LOGIN_ID = 'login_id';
	const PROPERTY_LOGIN_USER_ID = 'login_user_id';
	const PROPERTY_LOGIN_DATE = 'login_date';
	const PROPERTY_LOGIN_IP = 'login_ip';
	const PROPERTY_COURSE = 'course';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackEOnline object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackEOnline($defaultProperties = array ())
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
		return array (self :: PROPERTY_LOGIN_ID, self :: PROPERTY_LOGIN_USER_ID, self :: PROPERTY_LOGIN_DATE, self :: PROPERTY_LOGIN_IP, self :: PROPERTY_COURSE);
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
	 * Returns the login_id of this Dokeos185TrackEOnline.
	 * @return the login_id.
	 */
	function get_login_id()
	{
		return $this->get_default_property(self :: PROPERTY_LOGIN_ID);
	}

	/**
	 * Returns the login_user_id of this Dokeos185TrackEOnline.
	 * @return the login_user_id.
	 */
	function get_login_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_LOGIN_USER_ID);
	}

	/**
	 * Returns the login_date of this Dokeos185TrackEOnline.
	 * @return the login_date.
	 */
	function get_login_date()
	{
		return $this->get_default_property(self :: PROPERTY_LOGIN_DATE);
	}

	/**
	 * Returns the login_ip of this Dokeos185TrackEOnline.
	 * @return the login_ip.
	 */
	function get_login_ip()
	{
		return $this->get_default_property(self :: PROPERTY_LOGIN_IP);
	}

	/**
	 * Returns the course of this Dokeos185TrackEOnline.
	 * @return the course.
	 */
	function get_course()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE);
	}

	function is_valid($array)
	{
		$course = $array['course'];
	}
	
	function convert_to_lcms($array)
	{	
		$course = $array['course'];
	}
	
	static function get_all($parameters)
	{
		self :: $mgdm = $parameters['mgdm'];
		
		$db = 'statistics_database';
		$tablename = 'track_e_online';
		$classname = 'Dokeos185TrackEOnline';
			
		return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name);	
	}

}

?>