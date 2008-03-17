<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcoursedescription.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/category/category.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/description/description.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublication.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Course Description
 *
 * @author Sven Vanpoucke
 */

class Dokeos185CourseDescription extends ImportCourseDescription
{
	private static $mgdm;
	
	/**
	 * course description properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_CONTENT = 'content';
	
	/**
	 * Default properties of the course description object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new course description object.
	 * @param array $defaultProperties The default properties of the course description
	 *                                 object. Associative array.
	 */
	function Dokeos185CourseDescription($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this course description object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this course description.
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE,
						self :: PROPERTY_CONTENT);
	}
	
	/**
	 * Sets a default property of this course description by name.
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
	 * Returns the id of this course description.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the title of this course description.
	 * @return String The title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	/**
	 * Returns the content of this course content.
	 * @return String The content.
	 */
	function get_content()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT);
	}
	
	function is_valid_course_description($course)
	{	
		if(!$this->get_id() || !($this->get_title() || $this->get_content()))
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.description');
			return false;
		}
		return true;
	}
	
	function convert_to_new_course_description($course)
	{	
		$lcms_content = new Description();
		
		if(!$this->get_title())
			$lcms_calendar_event->set_title(substr($this->get_content(),0,20));
		else
			$lcms_calendar_event->set_title($this->get_title());
		
		if(!$this->get_content())
			$lcms_calendar_event->set_description($this->get_title());
		else
			$lcms_calendar_event->set_description($this->get_content());
		
		$user_id = self :: $mgdm->get_id_reference(self :: $mgdm->get_old_admin_id(), 'user_user');
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');
		
		// Category for contents already exists?
		$lcms_category_id = self :: $mgdm->get_parent_id($user_id, 'category',
			Translation :: get_lang('descriptions'));
		if(!$lcms_category_id)
		{
			//Create category for tool in lcms
			$lcms_repository_category = new Category();
			$lcms_repository_category->set_owner_id($user_id);
			$lcms_repository_category->set_title(Translation :: get_lang('descriptions'));
			$lcms_repository_category->set_description('...');
	
			//Retrieve repository id from course
			$repository_id = self :: $mgdm->get_parent_id($user_id, 
				'category', Translation :: get_lang('MyRepository'));
			$lcms_repository_category->set_parent_id($repository_id);
			
			//Create category in database
			$lcms_repository_category->create();
			
			$lcms_content->set_parent_id($lcms_repository_category->get_id());
		}
		else
		{
			$lcms_content->set_parent_id($lcms_category_id);	
		}
		
		$lcms_content->set_owner_id($user_id);
		$lcms_content->create();
		
		$publication = new LearningObjectPublication();
			
		$publication->set_learning_object($lcms_content);
		$publication->set_course_id($new_course_code);
		$publication->set_publisher_id($user_id);
		$publication->set_tool('description');
		$publication->set_category_id(0);
		$publication->set_from_date(0);
		$publication->set_to_date(0);
		
		$now = time();
		$publication->set_publication_date($now);
		$publication->set_modified_date($now);
		
		$publication->set_display_order_index(0);
		$publication->set_email_sent(0);
		$publication->set_hidden(0);
		
		//create publication in database
		$publication->create();
		
		return $lcms_content;
		
	}
	
	static function get_all_course_descriptions($db, $mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_course_descriptions($db);
	}
}
?>