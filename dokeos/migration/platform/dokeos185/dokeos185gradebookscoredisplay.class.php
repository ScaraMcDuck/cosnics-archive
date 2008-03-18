<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 gradebook_score_display
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GradebookScoreDisplay
{
	/**
	 * Dokeos185GradebookScoreDisplay properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_SCORE = 'score';
	const PROPERTY_DISPLAY = 'display';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185GradebookScoreDisplay object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185GradebookScoreDisplay($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_SCORE, SELF :: PROPERTY_DISPLAY);
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
	 * Returns the id of this Dokeos185GradebookScoreDisplay.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the score of this Dokeos185GradebookScoreDisplay.
	 * @return the score.
	 */
	function get_score()
	{
		return $this->get_default_property(self :: PROPERTY_SCORE);
	}

	/**
	 * Returns the display of this Dokeos185GradebookScoreDisplay.
	 * @return the display.
	 */
	function get_display()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY);
	}


}

?>