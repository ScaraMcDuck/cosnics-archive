<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 track_e_links
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackELinks
{
	/**
	 * Dokeos185TrackELinks properties
	 */
	const PROPERTY_LINKS_ID = 'links_id';
	const PROPERTY_LINKS_USER_ID = 'links_user_id';
	const PROPERTY_LINKS_DATE = 'links_date';
	const PROPERTY_LINKS_COURS_ID = 'links_cours_id';
	const PROPERTY_LINKS_LINK_ID = 'links_link_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackELinks object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackELinks($defaultProperties = array ())
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
		return array (self :: PROPERTY_LINKS_ID, self :: PROPERTY_LINKS_USER_ID, self :: PROPERTY_LINKS_DATE, self :: PROPERTY_LINKS_COURS_ID, self :: PROPERTY_LINKS_LINK_ID);
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
	 * Returns the links_id of this Dokeos185TrackELinks.
	 * @return the links_id.
	 */
	function get_links_id()
	{
		return $this->get_default_property(self :: PROPERTY_LINKS_ID);
	}

	/**
	 * Returns the links_user_id of this Dokeos185TrackELinks.
	 * @return the links_user_id.
	 */
	function get_links_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_LINKS_USER_ID);
	}

	/**
	 * Returns the links_date of this Dokeos185TrackELinks.
	 * @return the links_date.
	 */
	function get_links_date()
	{
		return $this->get_default_property(self :: PROPERTY_LINKS_DATE);
	}

	/**
	 * Returns the links_cours_id of this Dokeos185TrackELinks.
	 * @return the links_cours_id.
	 */
	function get_links_cours_id()
	{
		return $this->get_default_property(self :: PROPERTY_LINKS_COURS_ID);
	}

	/**
	 * Returns the links_link_id of this Dokeos185TrackELinks.
	 * @return the links_link_id.
	 */
	function get_links_link_id()
	{
		return $this->get_default_property(self :: PROPERTY_LINKS_LINK_ID);
	}


}

?>