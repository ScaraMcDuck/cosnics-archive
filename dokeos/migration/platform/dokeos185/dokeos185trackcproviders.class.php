<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 track_c_providers
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCProviders
{
	/**
	 * Dokeos185TrackCProviders properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_PROVIDER = 'provider';
	const PROPERTY_COUNTER = 'counter';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackCProviders object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackCProviders($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_PROVIDER, SELF :: PROPERTY_COUNTER);
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
	 * Returns the id of this Dokeos185TrackCProviders.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185TrackCProviders.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the provider of this Dokeos185TrackCProviders.
	 * @return the provider.
	 */
	function get_provider()
	{
		return $this->get_default_property(self :: PROPERTY_PROVIDER);
	}

	/**
	 * Sets the provider of this Dokeos185TrackCProviders.
	 * @param provider
	 */
	function set_provider($provider)
	{
		$this->set_default_property(self :: PROPERTY_PROVIDER, $provider);
	}
	/**
	 * Returns the counter of this Dokeos185TrackCProviders.
	 * @return the counter.
	 */
	function get_counter()
	{
		return $this->get_default_property(self :: PROPERTY_COUNTER);
	}

	/**
	 * Sets the counter of this Dokeos185TrackCProviders.
	 * @param counter
	 */
	function set_counter($counter)
	{
		$this->set_default_property(self :: PROPERTY_COUNTER, $counter);
	}

}

?>