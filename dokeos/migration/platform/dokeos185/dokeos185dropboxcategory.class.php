<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 dropbox_category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxCategory
{
	/**
	 * Dokeos185DropboxCategory properties
	 */
	const PROPERTY_CAT_ID = 'cat_id';
	const PROPERTY_CAT_NAME = 'cat_name';
	const PROPERTY_RECEIVED = 'received';
	const PROPERTY_SENT = 'sent';
	const PROPERTY_USER_ID = 'user_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185DropboxCategory object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185DropboxCategory($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_CAT_ID, SELF :: PROPERTY_CAT_NAME, SELF :: PROPERTY_RECEIVED, SELF :: PROPERTY_SENT, SELF :: PROPERTY_USER_ID);
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
	 * Returns the cat_id of this Dokeos185DropboxCategory.
	 * @return the cat_id.
	 */
	function get_cat_id()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_ID);
	}

	/**
	 * Returns the cat_name of this Dokeos185DropboxCategory.
	 * @return the cat_name.
	 */
	function get_cat_name()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_NAME);
	}

	/**
	 * Returns the received of this Dokeos185DropboxCategory.
	 * @return the received.
	 */
	function get_received()
	{
		return $this->get_default_property(self :: PROPERTY_RECEIVED);
	}

	/**
	 * Returns the sent of this Dokeos185DropboxCategory.
	 * @return the sent.
	 */
	function get_sent()
	{
		return $this->get_default_property(self :: PROPERTY_SENT);
	}

	/**
	 * Returns the user_id of this Dokeos185DropboxCategory.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}


}

?>