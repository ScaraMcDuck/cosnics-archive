<?php
/**
 * @package application.lib.weblcms.group
 * @author Bart Mollet
 */
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
/**
 * This class represents a group of users in a course in the weblcms.
 *
 * To access the values of the properties, this class and its subclasses should
 * provide accessor methods. The names of the properties should be defined as
 * class constants, for standardization purposes. It is recommended that the
 * names of these constants start with the string "PROPERTY_".
 *
 */
class Group
{
	const PROPERTY_ID = 'id';
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_NAME = 'name';

	private $id;
	private $course_code;
	private $defaultProperties;

	/**
	 * Creates a new group object.
	 * @param int $id The numeric ID of the group object. May be omitted if
	 * creating a new object.
	 * @param string $course_code The code of the course in which this group is
	 * created.
	 * @param array $defaultProperties The default properties of the group
	 * object. Associative array.
	 */
	function Group($id = null, $course_code , $defaultProperties = array ())
	{
		$this->id = $id;
		$this->course_code = $course_code;
		$this->defaultProperties = $defaultProperties;
	}
    /**
	 * Gets a default property of this group object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties of this group object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Sets a default property of this group object by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	/**
	 * Get the default properties of all groups.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_NAME);
	}
	function get_id()
	{
		return $this->id;
	}
	function get_course_code()
	{
		return $this->course_code;
	}
	function get_name()
	{
		return $this->get_default_property(self::PROPERTY_NAME);
	}
	function set_name($name)
	{
		return $this->set_default_property(self::PROPERTY_NAME,$name);
	}
	/**
	 * Deletes the group object from persistent storage
	 * @return boolean
	 */
	function delete()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->delete_group($this->get_id());
	}

	/**
	 * Creates the group object in persistent storage
	 * @return boolean
	 */
	function create()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->create_group($this);
	}

	/**
	 * Updates the group object in persistent storage
	 * @return boolean
	 */
	function update()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$success = $wdm->update_group($this);
		if (!$success)
		{
			return false;
		}
		return true;
	}
}
?>