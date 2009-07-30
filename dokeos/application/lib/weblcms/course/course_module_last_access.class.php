<?php 
/**
 * weblcms
 */

/**
 * This class describes a CourseModuleLastAccess data object
 *
 * @author Hans De Bisschop
 */
class CourseModuleLastAccess
{
	const CLASS_NAME = __CLASS__;

	/**
	 * CourseModuleLastAccess properties
	 */
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_MODULE_NAME = 'module_name';
	const PROPERTY_CATEGORY_ID = 'category_id';
	const PROPERTY_ACCESS_DATE = 'access_date';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new CourseModuleLastAccess object
	 * @param array $defaultProperties The default properties
	 */
	function CourseModuleLastAccess($defaultProperties = array ())
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
		return array (self :: PROPERTY_COURSE_CODE, self :: PROPERTY_USER_ID, self :: PROPERTY_MODULE_NAME, self :: PROPERTY_CATEGORY_ID, self :: PROPERTY_ACCESS_DATE);
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
	 * Returns the course_code of this CourseModuleLastAccess.
	 * @return the course_code.
	 */
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}

	/**
	 * Sets the course_code of this CourseModuleLastAccess.
	 * @param course_code
	 */
	function set_course_code($course_code)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
	}
	/**
	 * Returns the user_id of this CourseModuleLastAccess.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this CourseModuleLastAccess.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	/**
	 * Returns the module_name of this CourseModuleLastAccess.
	 * @return the module_name.
	 */
	function get_module_name()
	{
		return $this->get_default_property(self :: PROPERTY_MODULE_NAME);
	}

	/**
	 * Sets the module_name of this CourseModuleLastAccess.
	 * @param module_name
	 */
	function set_module_name($module_name)
	{
		$this->set_default_property(self :: PROPERTY_MODULE_NAME, $module_name);
	}
	/**
	 * Returns the category_id of this CourseModuleLastAccess.
	 * @return the category_id.
	 */
	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
	}

	/**
	 * Sets the category_id of this CourseModuleLastAccess.
	 * @param category_id
	 */
	function set_category_id($category_id)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
	}
	/**
	 * Returns the access_date of this CourseModuleLastAccess.
	 * @return the access_date.
	 */
	function get_access_date()
	{
		return $this->get_default_property(self :: PROPERTY_ACCESS_DATE);
	}

	/**
	 * Sets the access_date of this CourseModuleLastAccess.
	 * @param access_date
	 */
	function set_access_date($access_date)
	{
		$this->set_default_property(self :: PROPERTY_ACCESS_DATE, $access_date);
	}

	function delete()
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->delete_course_module_last_access($this);
	}

	function create()
	{
		$dm = WeblcmsDataManager :: get_instance();
		//$this->set_id($dm->get_next_course_module_last_access_id());
       	return $dm->create_course_module_last_access($this);
	}

	function update()
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->update_course_module_last_access($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>