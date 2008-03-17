<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 language
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Language
{
	/**
	 * Dokeos185Language properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_ORIGINAL_NAME = 'original_name';
	const PROPERTY_ENGLISH_NAME = 'english_name';
	const PROPERTY_ISOCODE = 'isocode';
	const PROPERTY_DOKEOS_FOLDER = 'dokeos_folder';
	const PROPERTY_AVAILABLE = 'available';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185Language object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Language($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_ORIGINAL_NAME, SELF :: PROPERTY_ENGLISH_NAME, SELF :: PROPERTY_ISOCODE, SELF :: PROPERTY_DOKEOS_FOLDER, SELF :: PROPERTY_AVAILABLE);
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
	 * Returns the id of this Dokeos185Language.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185Language.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the original_name of this Dokeos185Language.
	 * @return the original_name.
	 */
	function get_original_name()
	{
		return $this->get_default_property(self :: PROPERTY_ORIGINAL_NAME);
	}

	/**
	 * Sets the original_name of this Dokeos185Language.
	 * @param original_name
	 */
	function set_original_name($original_name)
	{
		$this->set_default_property(self :: PROPERTY_ORIGINAL_NAME, $original_name);
	}
	/**
	 * Returns the english_name of this Dokeos185Language.
	 * @return the english_name.
	 */
	function get_english_name()
	{
		return $this->get_default_property(self :: PROPERTY_ENGLISH_NAME);
	}

	/**
	 * Sets the english_name of this Dokeos185Language.
	 * @param english_name
	 */
	function set_english_name($english_name)
	{
		$this->set_default_property(self :: PROPERTY_ENGLISH_NAME, $english_name);
	}
	/**
	 * Returns the isocode of this Dokeos185Language.
	 * @return the isocode.
	 */
	function get_isocode()
	{
		return $this->get_default_property(self :: PROPERTY_ISOCODE);
	}

	/**
	 * Sets the isocode of this Dokeos185Language.
	 * @param isocode
	 */
	function set_isocode($isocode)
	{
		$this->set_default_property(self :: PROPERTY_ISOCODE, $isocode);
	}
	/**
	 * Returns the dokeos_folder of this Dokeos185Language.
	 * @return the dokeos_folder.
	 */
	function get_dokeos_folder()
	{
		return $this->get_default_property(self :: PROPERTY_DOKEOS_FOLDER);
	}

	/**
	 * Sets the dokeos_folder of this Dokeos185Language.
	 * @param dokeos_folder
	 */
	function set_dokeos_folder($dokeos_folder)
	{
		$this->set_default_property(self :: PROPERTY_DOKEOS_FOLDER, $dokeos_folder);
	}
	/**
	 * Returns the available of this Dokeos185Language.
	 * @return the available.
	 */
	function get_available()
	{
		return $this->get_default_property(self :: PROPERTY_AVAILABLE);
	}

	/**
	 * Sets the available of this Dokeos185Language.
	 * @param available
	 */
	function set_available($available)
	{
		$this->set_default_property(self :: PROPERTY_AVAILABLE, $available);
	}

}

?>