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
 
class Dokeos185CourseRelClass extends Import{

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
	
	/**
	 * Sets the course_code of this rel_class.
	 * @param String $course_code The course_code.
	 */
	function set_course_code($course_code)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
	}
	
	/**
	 * Sets the class_id of this rel_class.
	 * @param int $class_id The class_id.
	 */
	function set_class_id($class_id)
	{
		$this->set_default_property(self :: PROPERTY_CLASS_ID, $class_id);
	}
}
?>