<?php

require_once dirname(__FILE__).'/repository_data_manager.class.php';
require_once Path :: get_common_path() . 'data_class.class.php';

/**
 *  @author Sven Vanpoucke
 */

class UserViewRelLearningObject extends DataClass
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_VIEW_ID = 'view_id';
	const PROPERTY_LEARNING_OBJECT_TYPE = 'learning_object_type';
	const PROPERTY_VISIBILITY = 'visibility';
	
	/**
	 * Get the default properties of all user_view_rel_learning_objects.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_VIEW_ID, self :: PROPERTY_VISIBILITY, self :: PROPERTY_LEARNING_OBJECT_TYPE);
	}
	
	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return RepositoryDataManager :: get_instance();	
	}
	
	/**
	 * Returns the view_id of this user_view_rel_learning_object.
	 * @return int The view_id.
	 */
	function get_view_id()
	{
		return $this->get_default_property(self :: PROPERTY_VIEW_ID);
	}
	
	/**
	 * Returns the name of this user_view_rel_learning_object.
	 * @return String The name
	 */
	function get_visibility()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBILITY);
	}
	
	/**
	 * Sets the user_view_rel_learning_object_view_id of this user_view_rel_learning_object.
	 * @param int $user_view_rel_learning_object_view_id The user_view_rel_learning_object_view_id.
	 */
	function set_view_id($view_id)
	{
		$this->set_default_property(self :: PROPERTY_VIEW_ID, $view_id);
	}		
	
	/**
	 * Sets the name of this user_view_rel_learning_object.
	 * @param String $name the name.
	 */
	function set_visibility($visibility)
	{
		$this->set_default_property(self :: PROPERTY_VISIBILITY, $visibility);
	}
	
	function get_learning_object_type()
	{
		return $this->get_default_property(self :: PROPERTY_LEARNING_OBJECT_TYPE);
	}
	
	function set_learning_object_type($learning_object_type)
	{
		$this->set_default_property(self :: PROPERTY_LEARNING_OBJECT_TYPE, $learning_object_type);
	}
	
	function create()
	{
		$gdm = RepositoryDataManager :: get_instance();
		return $gdm->create_user_view_rel_learning_object($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>