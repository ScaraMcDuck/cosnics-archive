<?php

/**
 * @package migration.platform.dokeos185
 */
 
require_once dirname(__FILE__) . '/../../lib/import/importgradebookcategory.class.php';

/**
 * This class represents an old Dokeos 1.8.5 gradebook category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GradebookCategory extends ImportGradebookCategory
{
	/**
	 * Migration data manager
	 */
	private static $mgdm;
	
	/**
	 * Gradebook Category properties
	 */	 
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_PARENT_ID = 'parent_id';
	const PROPERTY_WEIGHT = 'weight';
	const PROPERTY_VISIBLE = 'visible';
	const PROPERTY_CERTIF_MIN_SCORE = 'certif_min_score';
	
	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new dokeos185 Gradebook Category object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185GradebookCategory($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION,
					  self :: PROPERTY_USER_ID, self :: PROPERTY_COURSE_CODE, 
					  self :: PROPERTY_PARENT_ID);
	}
	
	/**
	 * Sets a default property by name.
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
	 * Returns the id of this gradebook category.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	 
	/**
	 * Returns the name of this gradebook category.
	 * @return string the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	/**
	 * Returns the description of this gradebook category.
	 * @return string the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	/**
	 * Returns the user_id of this gradebook category.
	 * @return date the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	/**
	 * Returns the course_code of this gradebook category.
	 * @return int the course_code.
	 */
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}
	
	/**
	 * Returns the parent_id of this gradebook category.
	 * @return int the parent_id.
	 */
	function get_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ID);
	}
	
	/**
	 * Returns the weight of this announcement.
	 * @return string the weight.
	 */
	function get_weight()
	{
		return $this->get_default_property(self :: PROPERTY_WEIGHT);
	}
	
	/**
	 * Returns the visible of this announcement.
	 * @return string the visible.
	 */
	function get_visible()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE);
	}
	
	/**
	 * Returns the certif_min_score of this announcement.
	 * @return date the certif_min_score.
	 */
	function get_certif_min_score()
	{
		return $this->get_default_property(self :: PROPERTY_CERTIF_MIN_SCORE);
	}
	
	function is_valid_gradebook_category($course)
	{
		
	}
	
	function convert_to_new_gradebook_category($course)
	{
		
	}
	
	static function get_all_gradebook_categories($mgdm)
	{

	}
	
}
?>