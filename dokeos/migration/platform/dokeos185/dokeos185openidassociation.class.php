<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 openid_association
 *
 * @author Sven Vanpoucke
 */
class Dokeos185OpenidAssociation
{
	/**
	 * Dokeos185OpenidAssociation properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_IDP_ENDPOINT_URI = 'idp_endpoint_uri';
	const PROPERTY_SESSION_TYPE = 'session_type';
	const PROPERTY_ASSOC_HANDLE = 'assoc_handle';
	const PROPERTY_ASSOC_TYPE = 'assoc_type';
	const PROPERTY_EXPIRES_IN = 'expires_in';
	const PROPERTY_MAC_KEY = 'mac_key';
	const PROPERTY_CREATED = 'created';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185OpenidAssociation object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185OpenidAssociation($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_IDP_ENDPOINT_URI, SELF :: PROPERTY_SESSION_TYPE, SELF :: PROPERTY_ASSOC_HANDLE, SELF :: PROPERTY_ASSOC_TYPE, SELF :: PROPERTY_EXPIRES_IN, SELF :: PROPERTY_MAC_KEY, SELF :: PROPERTY_CREATED);
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
	 * Returns the id of this Dokeos185OpenidAssociation.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185OpenidAssociation.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the idp_endpoint_uri of this Dokeos185OpenidAssociation.
	 * @return the idp_endpoint_uri.
	 */
	function get_idp_endpoint_uri()
	{
		return $this->get_default_property(self :: PROPERTY_IDP_ENDPOINT_URI);
	}

	/**
	 * Sets the idp_endpoint_uri of this Dokeos185OpenidAssociation.
	 * @param idp_endpoint_uri
	 */
	function set_idp_endpoint_uri($idp_endpoint_uri)
	{
		$this->set_default_property(self :: PROPERTY_IDP_ENDPOINT_URI, $idp_endpoint_uri);
	}
	/**
	 * Returns the session_type of this Dokeos185OpenidAssociation.
	 * @return the session_type.
	 */
	function get_session_type()
	{
		return $this->get_default_property(self :: PROPERTY_SESSION_TYPE);
	}

	/**
	 * Sets the session_type of this Dokeos185OpenidAssociation.
	 * @param session_type
	 */
	function set_session_type($session_type)
	{
		$this->set_default_property(self :: PROPERTY_SESSION_TYPE, $session_type);
	}
	/**
	 * Returns the assoc_handle of this Dokeos185OpenidAssociation.
	 * @return the assoc_handle.
	 */
	function get_assoc_handle()
	{
		return $this->get_default_property(self :: PROPERTY_ASSOC_HANDLE);
	}

	/**
	 * Sets the assoc_handle of this Dokeos185OpenidAssociation.
	 * @param assoc_handle
	 */
	function set_assoc_handle($assoc_handle)
	{
		$this->set_default_property(self :: PROPERTY_ASSOC_HANDLE, $assoc_handle);
	}
	/**
	 * Returns the assoc_type of this Dokeos185OpenidAssociation.
	 * @return the assoc_type.
	 */
	function get_assoc_type()
	{
		return $this->get_default_property(self :: PROPERTY_ASSOC_TYPE);
	}

	/**
	 * Sets the assoc_type of this Dokeos185OpenidAssociation.
	 * @param assoc_type
	 */
	function set_assoc_type($assoc_type)
	{
		$this->set_default_property(self :: PROPERTY_ASSOC_TYPE, $assoc_type);
	}
	/**
	 * Returns the expires_in of this Dokeos185OpenidAssociation.
	 * @return the expires_in.
	 */
	function get_expires_in()
	{
		return $this->get_default_property(self :: PROPERTY_EXPIRES_IN);
	}

	/**
	 * Sets the expires_in of this Dokeos185OpenidAssociation.
	 * @param expires_in
	 */
	function set_expires_in($expires_in)
	{
		$this->set_default_property(self :: PROPERTY_EXPIRES_IN, $expires_in);
	}
	/**
	 * Returns the mac_key of this Dokeos185OpenidAssociation.
	 * @return the mac_key.
	 */
	function get_mac_key()
	{
		return $this->get_default_property(self :: PROPERTY_MAC_KEY);
	}

	/**
	 * Sets the mac_key of this Dokeos185OpenidAssociation.
	 * @param mac_key
	 */
	function set_mac_key($mac_key)
	{
		$this->set_default_property(self :: PROPERTY_MAC_KEY, $mac_key);
	}
	/**
	 * Returns the created of this Dokeos185OpenidAssociation.
	 * @return the created.
	 */
	function get_created()
	{
		return $this->get_default_property(self :: PROPERTY_CREATED);
	}

	/**
	 * Sets the created of this Dokeos185OpenidAssociation.
	 * @param created
	 */
	function set_created($created)
	{
		$this->set_default_property(self :: PROPERTY_CREATED, $created);
	}

}

?>