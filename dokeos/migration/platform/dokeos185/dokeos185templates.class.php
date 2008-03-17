<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 templates
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Templates
{
	/**
	 * Dokeos185Templates properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_REF_DOC = 'ref_doc';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185Templates object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Templates($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_DESCRIPTION, SELF :: PROPERTY_COURSE_CODE, SELF :: PROPERTY_USER_ID, SELF :: PROPERTY_REF_DOC);
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
	 * Returns the id of this Dokeos185Templates.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185Templates.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the title of this Dokeos185Templates.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Sets the title of this Dokeos185Templates.
	 * @param title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	/**
	 * Returns the description of this Dokeos185Templates.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Sets the description of this Dokeos185Templates.
	 * @param description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	/**
	 * Returns the course_code of this Dokeos185Templates.
	 * @return the course_code.
	 */
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}

	/**
	 * Sets the course_code of this Dokeos185Templates.
	 * @param course_code
	 */
	function set_course_code($course_code)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
	}
	/**
	 * Returns the user_id of this Dokeos185Templates.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Dokeos185Templates.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	/**
	 * Returns the ref_doc of this Dokeos185Templates.
	 * @return the ref_doc.
	 */
	function get_ref_doc()
	{
		return $this->get_default_property(self :: PROPERTY_REF_DOC);
	}

	/**
	 * Sets the ref_doc of this Dokeos185Templates.
	 * @param ref_doc
	 */
	function set_ref_doc($ref_doc)
	{
		$this->set_default_property(self :: PROPERTY_REF_DOC, $ref_doc);
	}

}

?>