<?php 
/**
 * weblcms
 */

/**
 * This class describes a CourseModule data object
 *
 * @author Hans De Bisschop
 */
class CourseModule
{
	const CLASS_NAME = __CLASS__;

	/**
	 * CourseModule properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_NAME = 'name';
	const PROPERTY_VISIBLE = 'visible';
	const PROPERTY_SECTION = 'section';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new CourseModule object
	 * @param array $defaultProperties The default properties
	 */
	function CourseModule($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_NAME, self :: PROPERTY_VISIBLE, self :: PROPERTY_SECTION);
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
	 * Returns the id of this CourseModule.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this CourseModule.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the course_code of this CourseModule.
	 * @return the course_code.
	 */
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}

	/**
	 * Sets the course_code of this CourseModule.
	 * @param course_code
	 */
	function set_course_code($course_code)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
	}
	/**
	 * Returns the name of this CourseModule.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this CourseModule.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	/**
	 * Returns the visible of this CourseModule.
	 * @return the visible.
	 */
	function get_visible()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE);
	}

	/**
	 * Sets the visible of this CourseModule.
	 * @param visible
	 */
	function set_visible($visible)
	{
		$this->set_default_property(self :: PROPERTY_VISIBLE, $visible);
	}
	/**
	 * Returns the section of this CourseModule.
	 * @return the section.
	 */
	function get_section()
	{
		return $this->get_default_property(self :: PROPERTY_SECTION);
	}

	/**
	 * Sets the section of this CourseModule.
	 * @param section
	 */
	function set_section($section)
	{
		$this->set_default_property(self :: PROPERTY_SECTION, $section);
	}

	function delete()
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->delete_course_module($this);
	}

	function create()
	{
		$dm = WeblcmsDataManager :: get_instance();
		$this->set_id($dm->get_next_course_module_id());
       	return $dm->create_course_module($this);
	}

	function update()
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->update_course_module($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>