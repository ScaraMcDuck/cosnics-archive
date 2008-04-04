<?php 
/**
 * migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../../lib/import/importuserinfodef.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/userinfo_def/userinfo_def.class.php';
/**
 * This class presents a Dokeos185 userinfo_def
 *
 * @author Sven Vanpoucke
 */
class Dokeos185UserinfoDef extends ImportUserinfoDef
{
	/**
	 * Dokeos185UserinfoDef properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_COMMENT = 'comment';
	const PROPERTY_LINE_COUNT = 'line_count';
	const PROPERTY_RANK = 'rank';
	
	private static $mgdm;

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185UserinfoDef object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185UserinfoDef($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_COMMENT, self :: PROPERTY_LINE_COUNT, self :: PROPERTY_RANK);
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
	 * Returns the id of this Dokeos185UserinfoDef.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the title of this Dokeos185UserinfoDef.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Returns the comment of this Dokeos185UserinfoDef.
	 * @return the comment.
	 */
	function get_comment()
	{
		return $this->get_default_property(self :: PROPERTY_COMMENT);
	}

	/**
	 * Returns the line_count of this Dokeos185UserinfoDef.
	 * @return the line_count.
	 */
	function get_line_count()
	{
		return $this->get_default_property(self :: PROPERTY_LINE_COUNT);
	}

	/**
	 * Returns the rank of this Dokeos185UserinfoDef.
	 * @return the rank.
	 */
	function get_rank()
	{
		return $this->get_default_property(self :: PROPERTY_RANK);
	}

	function is_valid($parameters)
	{ 
		$course = $parameters['course'];
		
		if(!$this->get_title())
		{
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.userinfodef');
			return false;		
		}
		
		return true;
	}
	
	function convert_to_lcms($parameters)
	{	
		$course = $parameters['course'];

		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');
		$new_user_id = self :: $mgdm->get_owner($new_course_code);
	
		//userinfodef parameters
		$lcms_userinfodef = new UserinfoDef();

		// Category for userinfo already exists?
		$lcms_category_id = self :: $mgdm->get_parent_id($new_user_id, 'category',
			Translation :: get_lang('userinfos'));
		if(!$lcms_category_id)
		{
			//Create category for tool in lcms
			$lcms_repository_category = new Category();
			$lcms_repository_category->set_owner_id($new_user_id);
			$lcms_repository_category->set_title(Translation :: get_lang('userinfos'));
			$lcms_repository_category->set_description('...');
	
			//Retrieve repository id from course
			$repository_id = self :: $mgdm->get_parent_id($new_user_id, 
				'category', Translation :: get_lang('MyRepository'));
			$lcms_repository_category->set_parent_id($repository_id);
			
			//Create category in database
			$lcms_repository_category->create();
			
			$lcms_userinfodef->set_parent_id($lcms_repository_category->get_id());
		}
		else
		{
			$lcms_userinfodef->set_parent_id($lcms_category_id);	
		}

		$lcms_userinfodef->set_title($this->get_title());
		$lcms_userinfodef->set_description($this->get_title());
		$lcms_userinfodef->set_owner_id($new_user_id);
		
		if($this->get_comment())
			$lcms_userinfodef->set_comment($this->get_comment());
		
		//create userinfodef in database
		$lcms_userinfodef->create_all();
		
		
		return $lcms_userinfodef;
	}

	static function get_all($parameters)
	{
		self :: $mgdm = $parameters['mgdm'];
		
		$coursedb = $parameters['course']->get_db_name();
		$tablename = 'userinfo_def';
		$classname = 'Dokeos185UserinfoDef';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}

}

?>
