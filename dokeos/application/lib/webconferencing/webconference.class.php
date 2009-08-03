<?php 
/**
 * webconferencing
 */

require_once Path :: get_common_path() . 'data_class.class.php';

/**
 * This class describes a Webconference data object
 *
 * @author Stefaan Vanbillemont
 */
class Webconference extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Webconference properties
	 */
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_CONFKEY = 'confkey';
	const PROPERTY_CONFNAME = 'confname';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_DURATION = 'duration';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array (self :: PROPERTY_USER_ID, self :: PROPERTY_CONFKEY, self :: PROPERTY_CONFNAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_DURATION));
	}

	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return WebconferencingDataManager :: get_instance();	
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

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>