<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcourserelclass.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_rel_class
 *
 * @author David Van Wayenbergh
 */
 
class Dokeos185CourseRelClass extends Import
{

	/**
	 * course relation class properties
	 */
	const PROPERTY_CODE = 'course_code';
	const PROPERTY_CLASS_ID = 'class_id';
	
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
	function Dokeos185CourseRelClass($defaultProperties = array ())
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
		return array (self :: PROPERTY_CODE,self :: PROPERTY_CLASS_ID);
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
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
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
	 * RELATION CLASS GETTERS AND SETTERS
	 */
	 
	/**
	 * Returns the course_code of this rel_class.
	 * @return String The course_code.
	 */
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}
	
	/**
	 * Returns the class_id of this rel_class.
	 * @return int The class_id.
	 */
	function get_class_id()
	{
		return $this->get_default_property(self :: PROPERTY_CLASS_ID);
	}
	
	function is_valid_course_class_relation()
	{
		if(!$this->get_course_code() || !$this->get_class_id() ||
			self :: $mgdm->get_failed_element('dokeos_main.course', $this->get_course_code()) ||
			self :: $mgdm->get_failed_element('dokeos_main.class', $this->get_class_id()) )
		{
			self :: $mgdm->add_failed_element($this->get_course_id() . '-' . $this->get_class_code(),
				'dokeos_main.course_rel_class');
			return false;
		}
		
		return true;
	}
	
	/**
	 * Migration course user relation
	 */
	function convert_to_new_course_class_relation()
	{
		$lcms_course_class_relation = new CourseClassRelation();
		
		$course_id = self :: $mgdm->get_id_reference($this->get_user_id(), 'weblcms_course');
		if($course_id)
			$lcms_course_class_relation->set_user_id($course_id);
		
		$class_id = self :: $mgdm->get_id_reference($this->get_class_id(), 'classgroup_classgroup');
		if($class_id)
			$lcms_course_class_relation->set_classgroup_id($class_id);
		
		$lcms_course_class_relation->create();
		
		return $lcms_course_class_relation;
	}
	
	function get_all_course_rel_user($mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_course_rel_classes();
	}
}
?>