<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 gradebook_link
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GradebookLink
{
	/**
	 * Dokeos185GradebookLink properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_TYPE = 'type';
	const PROPERTY_REF_ID = 'ref_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_CATEGORY_ID = 'category_id';
	const PROPERTY_DATE = 'date';
	const PROPERTY_WEIGHT = 'weight';
	const PROPERTY_VISIBLE = 'visible';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185GradebookLink object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185GradebookLink($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_TYPE, SELF :: PROPERTY_REF_ID, SELF :: PROPERTY_USER_ID, SELF :: PROPERTY_COURSE_CODE, SELF :: PROPERTY_CATEGORY_ID, SELF :: PROPERTY_DATE, SELF :: PROPERTY_WEIGHT, SELF :: PROPERTY_VISIBLE);
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
	 * Returns the id of this Dokeos185GradebookLink.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185GradebookLink.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the type of this Dokeos185GradebookLink.
	 * @return the type.
	 */
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}

	/**
	 * Sets the type of this Dokeos185GradebookLink.
	 * @param type
	 */
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_TYPE, $type);
	}
	/**
	 * Returns the ref_id of this Dokeos185GradebookLink.
	 * @return the ref_id.
	 */
	function get_ref_id()
	{
		return $this->get_default_property(self :: PROPERTY_REF_ID);
	}

	/**
	 * Sets the ref_id of this Dokeos185GradebookLink.
	 * @param ref_id
	 */
	function set_ref_id($ref_id)
	{
		$this->set_default_property(self :: PROPERTY_REF_ID, $ref_id);
	}
	/**
	 * Returns the user_id of this Dokeos185GradebookLink.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Dokeos185GradebookLink.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	/**
	 * Returns the course_code of this Dokeos185GradebookLink.
	 * @return the course_code.
	 */
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}

	/**
	 * Sets the course_code of this Dokeos185GradebookLink.
	 * @param course_code
	 */
	function set_course_code($course_code)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
	}
	/**
	 * Returns the category_id of this Dokeos185GradebookLink.
	 * @return the category_id.
	 */
	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
	}

	/**
	 * Sets the category_id of this Dokeos185GradebookLink.
	 * @param category_id
	 */
	function set_category_id($category_id)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
	}
	/**
	 * Returns the date of this Dokeos185GradebookLink.
	 * @return the date.
	 */
	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}

	/**
	 * Sets the date of this Dokeos185GradebookLink.
	 * @param date
	 */
	function set_date($date)
	{
		$this->set_default_property(self :: PROPERTY_DATE, $date);
	}
	/**
	 * Returns the weight of this Dokeos185GradebookLink.
	 * @return the weight.
	 */
	function get_weight()
	{
		return $this->get_default_property(self :: PROPERTY_WEIGHT);
	}

	/**
	 * Sets the weight of this Dokeos185GradebookLink.
	 * @param weight
	 */
	function set_weight($weight)
	{
		$this->set_default_property(self :: PROPERTY_WEIGHT, $weight);
	}
	/**
	 * Returns the visible of this Dokeos185GradebookLink.
	 * @return the visible.
	 */
	function get_visible()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE);
	}

	/**
	 * Sets the visible of this Dokeos185GradebookLink.
	 * @param visible
	 */
	function set_visible($visible)
	{
		$this->set_default_property(self :: PROPERTY_VISIBLE, $visible);
	}

}

?>