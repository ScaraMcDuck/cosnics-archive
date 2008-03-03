<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcourse.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_rel_user
 *
 * @author David Van Wayenbergh
 */
 
class Dokeos185CourseRelUser extends Import{


	/**
	 * course relation user properties
	 */
	const PROPERTY_CODE = 'course_code';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_STATUS = 'status';
	const PROPERTY_ROLE = 'role';
	const PROPERTY_GROUP_ID = 'group_id';
	const PROPERTY_TUTOR_ID = 'tutor_id';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_USER_COURSE_CAT = 'user_course_cat';
	
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
	function Dokeos185CourseRelUser($defaultProperties = array ())
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
		return array (self :: PROPERTY_CODE, self::PROPERTY_USER_ID,
		self::PROPERTY_STATUS, self::PROPERTY_ROLE,
		self::PROPERTY_GROUP_ID,self::PROPERTY_TUTOR_ID,
		self::PROPERTY_SORT,self::PROPERTY_USER_COURSE_CAT);
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
	
	/**
	 * Migration course user relation
	 */
	function convert_to_new_course_rel_user()
	{
		$mgdm = MigrationDataManager :: getInstance('Dokeos185');
		
		//course_rel_user parameters
		$lcms_course_rel_user = new CourseUserRelation();
		$lcms_course_rel_user->set_course($this->get_course_code());
		
		$user_id = $mgdm->get_id_reference($this->get_user_id(), 'user_user');
		if($user_id)
			$lcms_course_rel_user->set_user($user_id);
		
		$lcms_course_rel_user->set_status($this->get_status());
		$lcms_course_rel_user->set_role($this->get_role());
		$lcms_course_rel_user->set_group($this->get_group_id());
		$lcms_course_rel_user->set_tutor($this->get_tutor_id());
		$lcms_course_rel_user->set_sort($this->get_sort());
		$lcms_course_rel_user->set_category($this->get_user_course_cat());
		
		//create user in database
		$lcms_course_rel_user->create();
	}
}
