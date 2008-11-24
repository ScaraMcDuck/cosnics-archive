<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';

class CourseSection
{
	const CLASS_NAME = __CLASS__;
	
	const TYPE_DISABLED = '0';
	const TYPE_TOOL = '1';
	const TYPE_LINK = '2';
	const TYPE_ADMIN = '3';
	
	const PROPERTY_ID = 'id';
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_NAME = 'name';
	const PROPERTY_TYPE = 'type';
	const PROPERTY_VISIBLE = 'visible';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	
	private $defaultProperties;
	
	function CourseSection($defaultProperties = array())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	function set_default_properties($defaultProperties = array())
	{
		$this->defaultProperties = $defaultProperties;
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Get the default properties of all courses.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_NAME, 
				      self :: PROPERTY_TYPE, self :: PROPERTY_VISIBLE, self :: PROPERTY_DISPLAY_ORDER);
	}
	
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}
	
	function set_course_code($course_code)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
	}
	
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}
	
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_TYPE, $type);
	}
	
	function get_visible()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE);
	}
	
	function set_visible($visible)
	{
		$this->set_default_property(self :: PROPERTY_VISIBLE, $visible);
	}
	
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}
	
	function set_display_order($display_order)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
	}
	
	function create()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$this->set_id($wdm->get_next_course_section_id());
		$this->set_display_order($wdm->get_next_course_section_display_order($this));
		$wdm->create_course_section($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>
