<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 forum_category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumCategory
{
	/**
	 * Dokeos185ForumCategory properties
	 */
	const PROPERTY_CAT_ID = 'cat_id';
	const PROPERTY_CAT_TITLE = 'cat_title';
	const PROPERTY_CAT_COMMENT = 'cat_comment';
	const PROPERTY_CAT_ORDER = 'cat_order';
	const PROPERTY_LOCKED = 'locked';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185ForumCategory object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185ForumCategory($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_CAT_ID, SELF :: PROPERTY_CAT_TITLE, SELF :: PROPERTY_CAT_COMMENT, SELF :: PROPERTY_CAT_ORDER, SELF :: PROPERTY_LOCKED);
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
	 * Returns the cat_id of this Dokeos185ForumCategory.
	 * @return the cat_id.
	 */
	function get_cat_id()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_ID);
	}

	/**
	 * Returns the cat_title of this Dokeos185ForumCategory.
	 * @return the cat_title.
	 */
	function get_cat_title()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_TITLE);
	}

	/**
	 * Returns the cat_comment of this Dokeos185ForumCategory.
	 * @return the cat_comment.
	 */
	function get_cat_comment()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_COMMENT);
	}

	/**
	 * Returns the cat_order of this Dokeos185ForumCategory.
	 * @return the cat_order.
	 */
	function get_cat_order()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_ORDER);
	}

	/**
	 * Returns the locked of this Dokeos185ForumCategory.
	 * @return the locked.
	 */
	function get_locked()
	{
		return $this->get_default_property(self :: PROPERTY_LOCKED);
	}


}

?>