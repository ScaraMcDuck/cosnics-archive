<?php
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
/**
 * $Id: learning_object_publication.class.php 18805 2009-03-05 09:33:09Z Samumon $
 * @package application.weblcms
 */
/**
 * This class represents a learning object publication.
 *
 * When publishing a learning object from the repository in the weblcms
 * application, a new object of this type is created.
 */
class LearningObjectPublicationUser
{
   /**#@+
    * Constant defining a property of the publication
 	*/
	const PROPERTY_COURSE_ID = 'course_code';
	const PROPERTY_TOOL = 'tool';
	const PROPERTY_USER_ID = 'user_id';


	function LearningObjectPublicationUser($course, $tool, $user)
	{
        $this->set_default_property(self :: PROPERTY_COURSE_ID, $course);
        $this->set_default_property(self :: PROPERTY_TOOL, $tool);
        $this->set_default_property(self :: PROPERTY_USER_ID, $user);
	}

	/**
	 * Gets a default property of this user object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties of this user.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	static function get_default_property_names()
	{
		return array (self :: PROPERTY_COURSE_ID, self :: PROPERTY_TOOL, self :: PROPERTY_USER_ID);
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Checks if the given identifier is the name of a default user
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
    
	function get_course()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_ID);
	}
	function get_tool()
	{
		return $this->get_default_property(self :: PROPERTY_TOOL);
	}
    function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
}
?>