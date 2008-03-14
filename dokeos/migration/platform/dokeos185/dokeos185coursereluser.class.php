<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcoursereluser.class.php';
require_once dirname(__FILE__).'/../../../application/lib/weblcms/course/courseuserrelation.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_rel_user
 *
 * @author David Van Wayenberghµ
 * @author Sven Vanpoucke
 */
 
class Dokeos185CourseRelUser extends Import
{
	/**
	 * Migration data manager
	 */
	private static $mgdm;

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
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
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
		return $this->get_default_property(self :: PROPERTY_CODE);
	}
	
	/**
	 * Returns the user_id of this rel_user.
	 * @return int The user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	/**
	 * Returns the status of this rel_user.
	 * @return int The status.
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_STATUS);
	}
	
	/**
	 * Returns the role of this rel_user.
	 * @return String The role.
	 */
	function get_role()
	{
		return $this->get_default_property(self :: PROPERTY_ROLE);
	}
	
	/**
	 * Returns the group_id of this rel_user.
	 * @return int The group_id.
	 */
	function get_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_GROUP_ID);
	}
	
	/**
	 * Returns the tutor_id of this rel_user.
	 * @return int The tutor_id.
	 */
	function get_tutor_id()
	{
		return $this->get_default_property(self :: PROPERTY_TUTOR_ID);
	}
	
	/**
	 * Returns the sort of this rel_user.
	 * @return int The sort.
	 */
	function get_sort()
	{
		return $this->get_default_property(self :: PROPERTY_SORT);
	}
	
	/**
	 * Returns the user_course_cat of this rel_user.
	 * @return int The user_course_cat.
	 */
	function get_user_course_cat()
	{
	return $this->get_default_property(self :: PROPERTY_USER_COURSE_CAT);
	}
	
	function is_valid_course_user_relation()
	{
		if(!$this->get_course_code() || !$this->get_user_id() || $this->get_status() == NULL
			|| $this->get_group_id() == NULL || $this->get_tutor_id() == NULL ||
			self :: $mgdm->get_failed_element('dokeos_main.course', $this->get_course_code()) ||
			self :: $mgdm->get_failed_element('dokeos_main.user', $this->get_user_id()) ||
			!self :: $mgdm->get_id_reference($this->get_course_code(), 'weblcms_course') ||
			!self :: $mgdm->get_id_reference($this->get_user_id(), 'user_user'))
		{
			self :: $mgdm->add_failed_element($this->get_user_id() . '-' . $this->get_course_code(),
				'dokeos_main.course_rel_user');
			return false;
		}
		
		return true;
	}
	
	/**
	 * Migration course user relation
	 */
	function convert_to_new_course_user_relation()
	{
		//course_rel_user parameters
		$lcms_course_rel_user = new CourseUserRelation();
		
		$course_code = self :: $mgdm->get_id_reference($this->get_course_code(), 'weblcms_course');
		if($course_code)
			$lcms_course_rel_user->set_course($course_code);
		
		$user_id = self :: $mgdm->get_id_reference($this->get_user_id(), 'user_user');
		if($user_id)
			$lcms_course_rel_user->set_user($user_id);
		
		$lcms_course_rel_user->set_status($this->get_status());
		$lcms_course_rel_user->set_role($this->get_role());
		$lcms_course_rel_user->set_group($this->get_group_id());
		
		$lcms_course_rel_user->set_tutor($this->get_tutor_id());
		
		$lcms_course_rel_user->set_sort($this->get_sort());
		
		$category_code = self :: $mgdm->get_id_reference($this->get_user_course_cat(), 'weblcms_course_user_category');
		if($category_code)
			$lcms_course_rel_user->set_category($category_code);
		else
			$lcms_course_rel_user->set_category(0);
		
		//create user in database
		$lcms_course_rel_user->create();
		
		return $lcms_course_rel_user;
	}
	
	function get_all_course_rel_user($mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_course_rel_user();
	}
}
