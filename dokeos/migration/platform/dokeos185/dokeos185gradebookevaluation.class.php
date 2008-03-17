<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 gradebook_evaluation
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GradebookEvaluation
{
	/**
	 * Dokeos185GradebookEvaluation properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_CATEGORY_ID = 'category_id';
	const PROPERTY_DATE = 'date';
	const PROPERTY_WEIGHT = 'weight';
	const PROPERTY_MAX = 'max';
	const PROPERTY_VISIBLE = 'visible';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185GradebookEvaluation object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185GradebookEvaluation($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_NAME, SELF :: PROPERTY_DESCRIPTION, SELF :: PROPERTY_USER_ID, SELF :: PROPERTY_COURSE_CODE, SELF :: PROPERTY_CATEGORY_ID, SELF :: PROPERTY_DATE, SELF :: PROPERTY_WEIGHT, SELF :: PROPERTY_MAX, SELF :: PROPERTY_VISIBLE);
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
	 * Returns the id of this Dokeos185GradebookEvaluation.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185GradebookEvaluation.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the name of this Dokeos185GradebookEvaluation.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this Dokeos185GradebookEvaluation.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	/**
	 * Returns the description of this Dokeos185GradebookEvaluation.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Sets the description of this Dokeos185GradebookEvaluation.
	 * @param description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	/**
	 * Returns the user_id of this Dokeos185GradebookEvaluation.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Dokeos185GradebookEvaluation.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	/**
	 * Returns the course_code of this Dokeos185GradebookEvaluation.
	 * @return the course_code.
	 */
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}

	/**
	 * Sets the course_code of this Dokeos185GradebookEvaluation.
	 * @param course_code
	 */
	function set_course_code($course_code)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
	}
	/**
	 * Returns the category_id of this Dokeos185GradebookEvaluation.
	 * @return the category_id.
	 */
	function get_category_id()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
	}

	/**
	 * Sets the category_id of this Dokeos185GradebookEvaluation.
	 * @param category_id
	 */
	function set_category_id($category_id)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
	}
	/**
	 * Returns the date of this Dokeos185GradebookEvaluation.
	 * @return the date.
	 */
	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}

	/**
	 * Sets the date of this Dokeos185GradebookEvaluation.
	 * @param date
	 */
	function set_date($date)
	{
		$this->set_default_property(self :: PROPERTY_DATE, $date);
	}
	/**
	 * Returns the weight of this Dokeos185GradebookEvaluation.
	 * @return the weight.
	 */
	function get_weight()
	{
		return $this->get_default_property(self :: PROPERTY_WEIGHT);
	}

	/**
	 * Sets the weight of this Dokeos185GradebookEvaluation.
	 * @param weight
	 */
	function set_weight($weight)
	{
		$this->set_default_property(self :: PROPERTY_WEIGHT, $weight);
	}
	/**
	 * Returns the max of this Dokeos185GradebookEvaluation.
	 * @return the max.
	 */
	function get_max()
	{
		return $this->get_default_property(self :: PROPERTY_MAX);
	}

	/**
	 * Sets the max of this Dokeos185GradebookEvaluation.
	 * @param max
	 */
	function set_max($max)
	{
		$this->set_default_property(self :: PROPERTY_MAX, $max);
	}
	/**
	 * Returns the visible of this Dokeos185GradebookEvaluation.
	 * @return the visible.
	 */
	function get_visible()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE);
	}

	/**
	 * Sets the visible of this Dokeos185GradebookEvaluation.
	 * @param visible
	 */
	function set_visible($visible)
	{
		$this->set_default_property(self :: PROPERTY_VISIBLE, $visible);
	}

}

?>