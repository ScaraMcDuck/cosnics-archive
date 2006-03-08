<?php
class LearningObjectPublication
{
	private $learningObject;
	
	private $course;
	
	private $properties;
	
	function LearningObjectPublication($learningObject, $course, $properties = array())
	{
		$this->learningObject = $learningObject;
		$this->course = $course;
		$this->properties = $properties;
	}
	
	function get_learning_object ()
	{
		return $this->learningObject;
	}
	
	function get_course_id()
	{
		return $this->course;
	}
	
	function get_properties()
	{
		return $this->properties;
	}
	
	function get_property($name)
	{
		return $this->properties[$name];
	}
	
	function set_learning_object($learningObject)
	{
		$this->learningObject = $learningObject;
	}

	function set_course_id($course)
	{
		$this->course = $course;
	}
	
	function set_properties($properties)
	{
		$this->properties = $properties;
	}
	
	function set_property($name, $value)
	{
		$this->properties[$name] = $value;
	}
} 
?>