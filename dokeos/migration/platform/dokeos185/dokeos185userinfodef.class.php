<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 userinfo_def
 *
 * @author Sven Vanpoucke
 */
class Dokeos185UserinfoDef
{
	/**
	 * Dokeos185UserinfoDef properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_COMMENT = 'comment';
	const PROPERTY_LINE_COUNT = 'line_count';
	const PROPERTY_RANK = 'rank';

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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_COMMENT, SELF :: PROPERTY_LINE_COUNT, SELF :: PROPERTY_RANK);
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
	 * Sets the id of this Dokeos185UserinfoDef.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
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
	 * Sets the title of this Dokeos185UserinfoDef.
	 * @param title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
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
	 * Sets the comment of this Dokeos185UserinfoDef.
	 * @param comment
	 */
	function set_comment($comment)
	{
		$this->set_default_property(self :: PROPERTY_COMMENT, $comment);
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
	 * Sets the line_count of this Dokeos185UserinfoDef.
	 * @param line_count
	 */
	function set_line_count($line_count)
	{
		$this->set_default_property(self :: PROPERTY_LINE_COUNT, $line_count);
	}
	/**
	 * Returns the rank of this Dokeos185UserinfoDef.
	 * @return the rank.
	 */
	function get_rank()
	{
		return $this->get_default_property(self :: PROPERTY_RANK);
	}

	/**
	 * Sets the rank of this Dokeos185UserinfoDef.
	 * @param rank
	 */
	function set_rank($rank)
	{
		$this->set_default_property(self :: PROPERTY_RANK, $rank);
	}

}

?>