<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importtoolintro.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/category/category.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/description/description.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublication.class.php';

/**
 * This class presents a Dokeos185 tool_intro
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ToolIntro extends ImportToolIntro
{
	private static $mgdm;
	
	/**
	 * Dokeos185ToolIntro properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_INTRO_TEXT = 'intro_text';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185ToolIntro object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185ToolIntro($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_INTRO_TEXT);
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
	 * Returns the id of this Dokeos185ToolIntro.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the intro_text of this Dokeos185ToolIntro.
	 * @return the intro_text.
	 */
	function get_intro_text()
	{
		return $this->get_default_property(self :: PROPERTY_INTRO_TEXT);
	}

	function is_valid($array)
	{
		$course = $array['course'];
		
		if(!$this->get_intro_text())
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.toolintro');
			return false;
		}
		return true;
	}
	
	function convert_to_lcms($array)
	{	
		$course = $array['course'];
		
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');
		$user_id = self :: $mgdm->get_owner($new_course_code);
		
		$lcms_tool_intro = new Description();
		$lcms_tool_intro->set_title($this->get_intro_text());
		
		$lcms_tool_intro->set_description($this->get_intro_text());
		
		// Category for contents already exists?
		$lcms_category_id = self :: $mgdm->get_parent_id($user_id, 'category',
			Translation :: get('descriptions'));
		if(!$lcms_category_id)
		{
			//Create category for tool in lcms
			$lcms_repository_category = new Category();
			$lcms_repository_category->set_owner_id($user_id);
			$lcms_repository_category->set_title(Translation :: get('descriptions'));
			$lcms_repository_category->set_description('...');
	
			//Retrieve repository id from course
			$repository_id = self :: $mgdm->get_parent_id($user_id, 
				'category', Translation :: get('MyRepository'));
			$lcms_repository_category->set_parent_id($repository_id);
			
			//Create category in database
			$lcms_repository_category->create();
			
			$lcms_tool_intro->set_parent_id($lcms_repository_category->get_id());
		}
		else
		{
			$lcms_tool_intro->set_parent_id($lcms_category_id);	
		}
		
		$lcms_tool_intro->set_owner_id($user_id);
		$lcms_tool_intro->create();
		
	   $publication = new LearningObjectPublication();
			
		$publication->set_learning_object($lcms_tool_intro);
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
		
		return $lcms_tool_intro;
		
	}
	
	static function get_all($parameters)
	{
		self :: $mgdm = $parameters['mgdm'];
		
		$db = $parameters['course']->get_db_name();
		$tablename = 'tool_intro';
		$classname = 'Dokeos185ToolIntro';
			
		return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name);	
	}

}

?>