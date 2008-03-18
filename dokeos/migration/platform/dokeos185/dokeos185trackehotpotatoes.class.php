<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 track_e_hotpotatoes
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEHotpotatoes
{
	/**
	 * Dokeos185TrackEHotpotatoes properties
	 */
	const PROPERTY_EXE_NAME = 'exe_name';
	const PROPERTY_EXE_USER_ID = 'exe_user_id';
	const PROPERTY_EXE_DATE = 'exe_date';
	const PROPERTY_EXE_COURS_ID = 'exe_cours_id';
	const PROPERTY_EXE_RESULT = 'exe_result';
	const PROPERTY_EXE_WEIGHTING = 'exe_weighting';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackEHotpotatoes object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackEHotpotatoes($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_EXE_NAME, SELF :: PROPERTY_EXE_USER_ID, SELF :: PROPERTY_EXE_DATE, SELF :: PROPERTY_EXE_COURS_ID, SELF :: PROPERTY_EXE_RESULT, SELF :: PROPERTY_EXE_WEIGHTING);
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
	 * Returns the exe_name of this Dokeos185TrackEHotpotatoes.
	 * @return the exe_name.
	 */
	function get_exe_name()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_NAME);
	}

	/**
	 * Returns the exe_user_id of this Dokeos185TrackEHotpotatoes.
	 * @return the exe_user_id.
	 */
	function get_exe_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_USER_ID);
	}

	/**
	 * Returns the exe_date of this Dokeos185TrackEHotpotatoes.
	 * @return the exe_date.
	 */
	function get_exe_date()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_DATE);
	}

	/**
	 * Returns the exe_cours_id of this Dokeos185TrackEHotpotatoes.
	 * @return the exe_cours_id.
	 */
	function get_exe_cours_id()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_COURS_ID);
	}

	/**
	 * Returns the exe_result of this Dokeos185TrackEHotpotatoes.
	 * @return the exe_result.
	 */
	function get_exe_result()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_RESULT);
	}

	/**
	 * Returns the exe_weighting of this Dokeos185TrackEHotpotatoes.
	 * @return the exe_weighting.
	 */
	function get_exe_weighting()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_WEIGHTING);
	}


}

?>