<?php
/**
 * @package users
 */
/**
 *	@author Sven Vanpoucke
 */

require_once Path :: get_common_path() . 'data_class.class.php';

class BuddyListCategory extends DataClass
{
	const CLASS_NAME					= __CLASS__;
	
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_USER_ID = 'user_id';

	/**
	 * Get the default properties of all users quota objects.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array (self :: PROPERTY_TITLE, self :: PROPERTY_USER_ID));
	}
	
	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return UserDataManager :: get_instance();	
	}
	
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}	
	
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}	
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>