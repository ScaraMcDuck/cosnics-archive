<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 quiz
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Quiz
{
	/**
	 * Dokeos185Quiz properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_SOUND = 'sound';
	const PROPERTY_TYPE = 'type';
	const PROPERTY_RANDOM = 'random';
	const PROPERTY_ACTIVE = 'active';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185Quiz object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Quiz($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_DESCRIPTION, SELF :: PROPERTY_SOUND, SELF :: PROPERTY_TYPE, SELF :: PROPERTY_RANDOM, SELF :: PROPERTY_ACTIVE);
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
	 * Returns the id of this Dokeos185Quiz.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the title of this Dokeos185Quiz.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Returns the description of this Dokeos185Quiz.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Returns the sound of this Dokeos185Quiz.
	 * @return the sound.
	 */
	function get_sound()
	{
		return $this->get_default_property(self :: PROPERTY_SOUND);
	}

	/**
	 * Returns the type of this Dokeos185Quiz.
	 * @return the type.
	 */
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}

	/**
	 * Returns the random of this Dokeos185Quiz.
	 * @return the random.
	 */
	function get_random()
	{
		return $this->get_default_property(self :: PROPERTY_RANDOM);
	}

	/**
	 * Returns the active of this Dokeos185Quiz.
	 * @return the active.
	 */
	function get_active()
	{
		return $this->get_default_property(self :: PROPERTY_ACTIVE);
	}


}

?>