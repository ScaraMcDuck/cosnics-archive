<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/import_course.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_rel_user
 *
 * @author David Van Wayenbergh
 */
 
class Dokeos185_Course_Rel_User extends Import{


	/**
	 * course relation user properties
	 */
	const PROPERTY_REL_USER_CODE = 'course_code';
	const PROPERTY_REL_USER_USER_ID = 'user_id';
	const PROPERTY_REL_USER_STATUS = 'status';
	const PROPERTY_REL_USER_ROLE = 'role';
	const PROPERTY_REL_USER_GROUP_ID = 'group_id';
	const PROPERTY_REL_USER_TUTOR_ID = 'tutor_id';
	const PROPERTY_REL_USER_SORT = 'sort';
	const PROPERTY_REL_USER_USER_COURSE_CAT = 'user_course_cat';
	
	/**
	 * Alfanumeric identifier of the course object.
	 */
	private $code;
	
	/**
	 * Default properties of the course object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new course object.
	 * @param array $defaultProperties The default properties of the user
	 *                                 object. Associative array.
	 */
	function Course($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this course object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this course.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all courses.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_USER_ID, self :: PROPERTY_LASTNAME, self :: PROPERTY_FIRSTNAME, self :: PROPERTY_USERNAME, self :: PROPERTY_PASSWORD, self :: PROPERTY_AUTH_SOURCE, self :: PROPERTY_EMAIL, self :: PROPERTY_STATUS, self :: PROPERTY_PLATFORMADMIN, self :: PROPERTY_PHONE, self :: PROPERTY_OFFICIAL_CODE, self ::PROPERTY_PICTURE_URI, self :: PROPERTY_CREATOR_ID, self :: PROPERTY_LANGUAGE, self :: PROPERTY_DISK_QUOTA, self :: PROPERTY_DATABASE_QUOTA, self :: PROPERTY_VERSION_QUOTA);
	}
	
	/**
	 * Sets a default property of this course by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default course
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	
	/**
	 * RELATION USER GETTERS AND SETTERS
	 */
	 
	/**
	 * Returns the course_code of this rel_user.
	 * @return String The course_code.
	 */
	function get_course_code()
	{
		return $this->course_code;
	}
	
	/**
	 * Returns the user_id of this rel_user.
	 * @return int The user_id.
	 */
	function get_user_id()
	{
		return $this->user_id;
	}
	
	/**
	 * Returns the status of this rel_user.
	 * @return int The status.
	 */
	function get_status()
	{
		return $this->user_id;
	}
	
	/**
	 * Returns the role of this rel_user.
	 * @return String The role.
	 */
	function get_role()
	{
		return $this->role;
	}
	
	/**
	 * Returns the group_id of this rel_user.
	 * @return int The group_id.
	 */
	function get_group_id()
	{
		return $this->group_id;
	}
	
	/**
	 * Returns the tutor_id of this rel_user.
	 * @return int The tutor_id.
	 */
	function get_tutor_id()
	{
		return $this->tutor_id;
	}
	
	/**
	 * Returns the sort of this rel_user.
	 * @return int The sort.
	 */
	function get_sort()
	{
		return $this->type;
	}
	
	/**
	 * Returns the user_course_cat of this rel_user.
	 * @return int The user_course_cat.
	 */
	function get_user_course_cat()
	{
		return $this->user_course_cat;
	}
	
	/**
	 * Sets the course_code of this rel_user.
	 * @param String $course_code The course_code.
	 */
	function set_course_code($course_code)
	{
		$this->course_code = $course_code;
	}
	
	/**
	 * Sets the user_id of this rel_user.
	 * @param int $user_id The user_id.
	 */
	function set_user_id($user_id)
	{
		$this->user_id = $user_id;
	}
	
	/**
	 * Sets the status of this rel_user.
	 * @param int $status The status.
	 */
	function set_course_code($course_code)
	{
		$this->course_code = $course_code;
	}
	
	/**
	 * Sets the role of this rel_user.
	 * @param String $role The role.
	 */
	function set_role($role)
	{
		$this->role = $role;
	}
	
	/**
	 * Sets the group_id of this rel_user.
	 * @param int $group_id The group_id.
	 */
	function set_group_id($group_id)
	{
		$this->group_id = $group_id;
	}
	
	/**
	 * Sets the tutor_id of this rel_user.
	 * @param int $tutor_id The tutor_id.
	 */
	function set_tutor_id($tutor_id)
	{
		$this->tutor_id = $tutor_id;
	}
	
	/**
	 * Sets the sort of this rel_user.
	 * @param int $course_code The sort.
	 */
	function set_sort($sort)
	{
		$this->type = $sort;
	}
	
	/**
	 * Sets the user_course_cat of this rel_user.
	 * @param int $user_course_cat The user_course_cat.
	 */
	function set_user_course_cat($user_course_cat)
	{
		$this->user_course_cat = $user_course_cat;
	}
}
