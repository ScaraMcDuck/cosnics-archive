<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 assignment_file
 *
 * @author Sven Vanpoucke
 */
class Dokeos185AssignmentFile
{
	/**
	 * Dokeos185AssignmentFile properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_ASSIGNMENT_ID = 'assignment_id';
	const PROPERTY_DOC_PATH = 'doc_path';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185AssignmentFile object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185AssignmentFile($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_ASSIGNMENT_ID, self :: PROPERTY_DOC_PATH);
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
	 * Returns the id of this Dokeos185AssignmentFile.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the assignment_id of this Dokeos185AssignmentFile.
	 * @return the assignment_id.
	 */
	function get_assignment_id()
	{
		return $this->get_default_property(self :: PROPERTY_ASSIGNMENT_ID);
	}

	/**
	 * Returns the doc_path of this Dokeos185AssignmentFile.
	 * @return the doc_path.
	 */
	function get_doc_path()
	{
		return $this->get_default_property(self :: PROPERTY_DOC_PATH);
	}


}

?>