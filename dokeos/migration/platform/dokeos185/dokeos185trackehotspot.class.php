<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 track_e_hotspot
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEHotspot
{
	/**
	 * Dokeos185TrackEHotspot properties
	 */
	const PROPERTY_HOTSPOT_ID = 'hotspot_id';
	const PROPERTY_HOTSPOT_USER_ID = 'hotspot_user_id';
	const PROPERTY_HOTSPOT_COURSE_CODE = 'hotspot_course_code';
	const PROPERTY_HOTSPOT_EXE_ID = 'hotspot_exe_id';
	const PROPERTY_HOTSPOT_QUESTION_ID = 'hotspot_question_id';
	const PROPERTY_HOTSPOT_ANSWER_ID = 'hotspot_answer_id';
	const PROPERTY_HOTSPOT_CORRECT = 'hotspot_correct';
	const PROPERTY_HOTSPOT_COORDINATE = 'hotspot_coordinate';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackEHotspot object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackEHotspot($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_HOTSPOT_ID, SELF :: PROPERTY_HOTSPOT_USER_ID, SELF :: PROPERTY_HOTSPOT_COURSE_CODE, SELF :: PROPERTY_HOTSPOT_EXE_ID, SELF :: PROPERTY_HOTSPOT_QUESTION_ID, SELF :: PROPERTY_HOTSPOT_ANSWER_ID, SELF :: PROPERTY_HOTSPOT_CORRECT, SELF :: PROPERTY_HOTSPOT_COORDINATE);
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
	 * Returns the hotspot_id of this Dokeos185TrackEHotspot.
	 * @return the hotspot_id.
	 */
	function get_hotspot_id()
	{
		return $this->get_default_property(self :: PROPERTY_HOTSPOT_ID);
	}

	/**
	 * Sets the hotspot_id of this Dokeos185TrackEHotspot.
	 * @param hotspot_id
	 */
	function set_hotspot_id($hotspot_id)
	{
		$this->set_default_property(self :: PROPERTY_HOTSPOT_ID, $hotspot_id);
	}
	/**
	 * Returns the hotspot_user_id of this Dokeos185TrackEHotspot.
	 * @return the hotspot_user_id.
	 */
	function get_hotspot_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_HOTSPOT_USER_ID);
	}

	/**
	 * Sets the hotspot_user_id of this Dokeos185TrackEHotspot.
	 * @param hotspot_user_id
	 */
	function set_hotspot_user_id($hotspot_user_id)
	{
		$this->set_default_property(self :: PROPERTY_HOTSPOT_USER_ID, $hotspot_user_id);
	}
	/**
	 * Returns the hotspot_course_code of this Dokeos185TrackEHotspot.
	 * @return the hotspot_course_code.
	 */
	function get_hotspot_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_HOTSPOT_COURSE_CODE);
	}

	/**
	 * Sets the hotspot_course_code of this Dokeos185TrackEHotspot.
	 * @param hotspot_course_code
	 */
	function set_hotspot_course_code($hotspot_course_code)
	{
		$this->set_default_property(self :: PROPERTY_HOTSPOT_COURSE_CODE, $hotspot_course_code);
	}
	/**
	 * Returns the hotspot_exe_id of this Dokeos185TrackEHotspot.
	 * @return the hotspot_exe_id.
	 */
	function get_hotspot_exe_id()
	{
		return $this->get_default_property(self :: PROPERTY_HOTSPOT_EXE_ID);
	}

	/**
	 * Sets the hotspot_exe_id of this Dokeos185TrackEHotspot.
	 * @param hotspot_exe_id
	 */
	function set_hotspot_exe_id($hotspot_exe_id)
	{
		$this->set_default_property(self :: PROPERTY_HOTSPOT_EXE_ID, $hotspot_exe_id);
	}
	/**
	 * Returns the hotspot_question_id of this Dokeos185TrackEHotspot.
	 * @return the hotspot_question_id.
	 */
	function get_hotspot_question_id()
	{
		return $this->get_default_property(self :: PROPERTY_HOTSPOT_QUESTION_ID);
	}

	/**
	 * Sets the hotspot_question_id of this Dokeos185TrackEHotspot.
	 * @param hotspot_question_id
	 */
	function set_hotspot_question_id($hotspot_question_id)
	{
		$this->set_default_property(self :: PROPERTY_HOTSPOT_QUESTION_ID, $hotspot_question_id);
	}
	/**
	 * Returns the hotspot_answer_id of this Dokeos185TrackEHotspot.
	 * @return the hotspot_answer_id.
	 */
	function get_hotspot_answer_id()
	{
		return $this->get_default_property(self :: PROPERTY_HOTSPOT_ANSWER_ID);
	}

	/**
	 * Sets the hotspot_answer_id of this Dokeos185TrackEHotspot.
	 * @param hotspot_answer_id
	 */
	function set_hotspot_answer_id($hotspot_answer_id)
	{
		$this->set_default_property(self :: PROPERTY_HOTSPOT_ANSWER_ID, $hotspot_answer_id);
	}
	/**
	 * Returns the hotspot_correct of this Dokeos185TrackEHotspot.
	 * @return the hotspot_correct.
	 */
	function get_hotspot_correct()
	{
		return $this->get_default_property(self :: PROPERTY_HOTSPOT_CORRECT);
	}

	/**
	 * Sets the hotspot_correct of this Dokeos185TrackEHotspot.
	 * @param hotspot_correct
	 */
	function set_hotspot_correct($hotspot_correct)
	{
		$this->set_default_property(self :: PROPERTY_HOTSPOT_CORRECT, $hotspot_correct);
	}
	/**
	 * Returns the hotspot_coordinate of this Dokeos185TrackEHotspot.
	 * @return the hotspot_coordinate.
	 */
	function get_hotspot_coordinate()
	{
		return $this->get_default_property(self :: PROPERTY_HOTSPOT_COORDINATE);
	}

	/**
	 * Sets the hotspot_coordinate of this Dokeos185TrackEHotspot.
	 * @param hotspot_coordinate
	 */
	function set_hotspot_coordinate($hotspot_coordinate)
	{
		$this->set_default_property(self :: PROPERTY_HOTSPOT_COORDINATE, $hotspot_coordinate);
	}

}

?>