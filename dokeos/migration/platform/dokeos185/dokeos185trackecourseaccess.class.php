<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 track_e_course_access
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackECourseAccess
{
	/**
	 * Dokeos185TrackECourseAccess properties
	 */
	const PROPERTY_COURSE_ACCESS_ID = 'course_access_id';
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_LOGIN_COURSE_DATE = 'login_course_date';
	const PROPERTY_LOGOUT_COURSE_DATE = 'logout_course_date';
	const PROPERTY_COUNTER = 'counter';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackECourseAccess object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackECourseAccess($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_COURSE_ACCESS_ID, SELF :: PROPERTY_COURSE_CODE, SELF :: PROPERTY_USER_ID, SELF :: PROPERTY_LOGIN_COURSE_DATE, SELF :: PROPERTY_LOGOUT_COURSE_DATE, SELF :: PROPERTY_COUNTER);
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
	 * Returns the course_access_id of this Dokeos185TrackECourseAccess.
	 * @return the course_access_id.
	 */
	function get_course_access_id()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_ACCESS_ID);
	}

	/**
	 * Sets the course_access_id of this Dokeos185TrackECourseAccess.
	 * @param course_access_id
	 */
	function set_course_access_id($course_access_id)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_ACCESS_ID, $course_access_id);
	}
	/**
	 * Returns the course_code of this Dokeos185TrackECourseAccess.
	 * @return the course_code.
	 */
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}

	/**
	 * Sets the course_code of this Dokeos185TrackECourseAccess.
	 * @param course_code
	 */
	function set_course_code($course_code)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
	}
	/**
	 * Returns the user_id of this Dokeos185TrackECourseAccess.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Dokeos185TrackECourseAccess.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	/**
	 * Returns the login_course_date of this Dokeos185TrackECourseAccess.
	 * @return the login_course_date.
	 */
	function get_login_course_date()
	{
		return $this->get_default_property(self :: PROPERTY_LOGIN_COURSE_DATE);
	}

	/**
	 * Sets the login_course_date of this Dokeos185TrackECourseAccess.
	 * @param login_course_date
	 */
	function set_login_course_date($login_course_date)
	{
		$this->set_default_property(self :: PROPERTY_LOGIN_COURSE_DATE, $login_course_date);
	}
	/**
	 * Returns the logout_course_date of this Dokeos185TrackECourseAccess.
	 * @return the logout_course_date.
	 */
	function get_logout_course_date()
	{
		return $this->get_default_property(self :: PROPERTY_LOGOUT_COURSE_DATE);
	}

	/**
	 * Sets the logout_course_date of this Dokeos185TrackECourseAccess.
	 * @param logout_course_date
	 */
	function set_logout_course_date($logout_course_date)
	{
		$this->set_default_property(self :: PROPERTY_LOGOUT_COURSE_DATE, $logout_course_date);
	}
	/**
	 * Returns the counter of this Dokeos185TrackECourseAccess.
	 * @return the counter.
	 */
	function get_counter()
	{
		return $this->get_default_property(self :: PROPERTY_COUNTER);
	}

	/**
	 * Sets the counter of this Dokeos185TrackECourseAccess.
	 * @param counter
	 */
	function set_counter($counter)
	{
		$this->set_default_property(self :: PROPERTY_COUNTER, $counter);
	}

}

?>