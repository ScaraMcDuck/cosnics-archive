<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 gradebook_result
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GradebookResult
{
	/**
	 * Dokeos185GradebookResult properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_EVALUATION_ID = 'evaluation_id';
	const PROPERTY_DATE = 'date';
	const PROPERTY_SCORE = 'score';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185GradebookResult object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185GradebookResult($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_EVALUATION_ID, self :: PROPERTY_DATE, self :: PROPERTY_SCORE);
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
	 * Returns the id of this Dokeos185GradebookResult.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the user_id of this Dokeos185GradebookResult.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Returns the evaluation_id of this Dokeos185GradebookResult.
	 * @return the evaluation_id.
	 */
	function get_evaluation_id()
	{
		return $this->get_default_property(self :: PROPERTY_EVALUATION_ID);
	}

	/**
	 * Returns the date of this Dokeos185GradebookResult.
	 * @return the date.
	 */
	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}

	/**
	 * Returns the score of this Dokeos185GradebookResult.
	 * @return the score.
	 */
	function get_score()
	{
		return $this->get_default_property(self :: PROPERTY_SCORE);
	}


}

?>