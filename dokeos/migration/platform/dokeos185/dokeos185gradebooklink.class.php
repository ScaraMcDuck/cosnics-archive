<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 gradebook_link
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_TYPE, self :: PROPERTY_REF_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_CATEGORY_ID, self :: PROPERTY_DATE, self :: PROPERTY_WEIGHT, self :: PROPERTY_VISIBLE);
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
	 * Returns the type of this Dokeos185GradebookLink.
	 * @return the type.
	 */
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
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
	 * Returns the user_id of this Dokeos185GradebookLink.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
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
	 * Returns the category_id of this Dokeos185GradebookLink.
	 * @return the category_id.
	 */
	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
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
	 * Returns the weight of this Dokeos185GradebookLink.
	 * @return the weight.
	 */
	function get_weight()
	{
		return $this->get_default_property(self :: PROPERTY_WEIGHT);
	}

	/**
	 * Returns the visible of this Dokeos185GradebookLink.
	 * @return the visible.
	 */
	function get_visible()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE);
	}


}

?>