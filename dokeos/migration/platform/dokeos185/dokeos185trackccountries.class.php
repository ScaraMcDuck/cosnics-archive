<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 track_c_countries
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCCountries
{
	/**
	 * Dokeos185TrackCCountries properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_CODE = 'code';
	const PROPERTY_COUNTRY = 'country';
	const PROPERTY_COUNTER = 'counter';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackCCountries object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackCCountries($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_CODE, SELF :: PROPERTY_COUNTRY, SELF :: PROPERTY_COUNTER);
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
	 * Returns the id of this Dokeos185TrackCCountries.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185TrackCCountries.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the code of this Dokeos185TrackCCountries.
	 * @return the code.
	 */
	function get_code()
	{
		return $this->get_default_property(self :: PROPERTY_CODE);
	}

	/**
	 * Sets the code of this Dokeos185TrackCCountries.
	 * @param code
	 */
	function set_code($code)
	{
		$this->set_default_property(self :: PROPERTY_CODE, $code);
	}
	/**
	 * Returns the country of this Dokeos185TrackCCountries.
	 * @return the country.
	 */
	function get_country()
	{
		return $this->get_default_property(self :: PROPERTY_COUNTRY);
	}

	/**
	 * Sets the country of this Dokeos185TrackCCountries.
	 * @param country
	 */
	function set_country($country)
	{
		$this->set_default_property(self :: PROPERTY_COUNTRY, $country);
	}
	/**
	 * Returns the counter of this Dokeos185TrackCCountries.
	 * @return the counter.
	 */
	function get_counter()
	{
		return $this->get_default_property(self :: PROPERTY_COUNTER);
	}

	/**
	 * Sets the counter of this Dokeos185TrackCCountries.
	 * @param counter
	 */
	function set_counter($counter)
	{
		$this->set_default_property(self :: PROPERTY_COUNTER, $counter);
	}

}

?>