<?php 
/**
 * webconferencing
 */

/**
 * This class describes a Webconference data object
 *
 * @author Stefaan Vanbillemont
 */
class Webconference
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Webconference properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_CONFKEY = 'confkey';
	const PROPERTY_CONFNAME = 'confname';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_DURATION = 'duration';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Webconference object
	 * @param array $defaultProperties The default properties
	 */
	function Webconference($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_CONFKEY, self :: PROPERTY_CONFNAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_DURATION);
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
	 * Returns the id of this Webconference.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Webconference.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Returns the user_id of this Webconference owner.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Webconference ownser.
	 * @param user_id
	 */
	
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	
	/**
	 * Returns the confkey of this Webconference.
	 * @return the confkey.
	 */
	function get_confkey()
	{
		return $this->get_default_property(self :: PROPERTY_CONFKEY);
	}

	/**
	 * Sets the confkey of this Webconference.
	 * @param confkey
	 */
	function set_confkey($confkey)
	{
		$this->set_default_property(self :: PROPERTY_CONFKEY, $confkey);
	}
	/**
	 * Returns the confname of this Webconference.
	 * @return the confname.
	 */
	function get_confname()
	{
		return $this->get_default_property(self :: PROPERTY_CONFNAME);
	}

	/**
	 * Sets the confname of this Webconference.
	 * @param confname
	 */
	function set_confname($confname)
	{
		$this->set_default_property(self :: PROPERTY_CONFNAME, $confname);
	}
	
	/**
	 * Returns the description of this Webconference.
	 * @return the conference description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Sets the description this Webconference.
	 * @param the conference description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	/**
	 * Returns the duration of this Webconference.
	 * @return the duration.
	 */
	function get_duration()
	{
		return $this->get_default_property(self :: PROPERTY_DURATION);
	}

	/**
	 * Sets the duration of this Webconference.
	 * @param duration
	 */
	function set_duration($duration)
	{
		$this->set_default_property(self :: PROPERTY_DURATION, $duration);
	}

	function delete()
	{
		$dm = WebconferencingDataManager :: get_instance();
		return $dm->delete_webconference($this);
	}

	function create()
	{
		$dm = WebconferencingDataManager :: get_instance();
		$this->set_id($dm->get_next_webconference_id());
       	return $dm->create_webconference($this);
	}

	function update()
	{
		$dm = WebconferencingDataManager :: get_instance();
		return $dm->update_webconference($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>