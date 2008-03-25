<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importlinkcategory.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublicationcategory.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Link Category
 *
 * @author David Van Wayenbergh
 */

class Dokeos185LinkCategory extends ImportLinkCategory
{
	private static $mgdm;
	
	/**
	 * link category properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_CATEGORY_TITLE = 'category_title';
	const PROPERTY_DESCRIPTION = 'description';
	
	/**
	 * Default properties of the link category object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new link category object.
	 * @param array $defaultProperties The default properties of the link category
	 *                                 object. Associative array.
	 */
	function Dokeos185LinkCategory($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this link category object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this link category.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all link categories.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_CATEGORY_TITLE,
						self :: PROPERTY_DESCRIPTION);
	}
	
	/**
	 * Sets a default property of this link category by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Sets the default properties of this link.
	 * @param array $defaultProperties An associative array containing the properties.
	 */
	function set_default_properties($defaultProperties)
	{
		return $this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Returns the id of this link category.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the category_title of this link category.
	 * @return String The category_title.
	 */
	function get_category_title()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_TITLE);
	}
	
	/**
	 * Returns the description of this link category.
	 * @return String The description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	function is_valid_link_category($course)
	{	
		if(!$this->get_id() || !($this->get_category_title() || $this->get_description()))
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.link');
			return false;
		}
		return true;
	}
	
	function convert_to_new_link_category($course)
	{	
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');
		
		$lcms_link_category = new LearningObjectPublicationCategory();
		
		$lcms_link_category->set_course($new_course_code);
		$lcms_link_category->set_tool('link');
		
		if(!$this->get_category_title())
			$lcms_link_category->set_title($this->get_description());
		else
			$lcms_link_category->set_title($this->get_category_title());
			
		$lcms_link_category->get_parent_category_id(0);
		
		$lcms_link_category->create();
		
		self :: $mgdm->add_id_reference($this->get_id(), $lcms_link_category->get_id(), $new_course_code . '.link_category');
		
		return $lcms_link_category;
		
	}
	
	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];
		
		$coursedb = $parameters['course'];
		$tablename = 'link_category';
		$classname = 'Dokeos185LinkCategory';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}
}
?>
