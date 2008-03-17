<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 blog
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Blog
{
	/**
	 * Dokeos185Blog properties
	 */
	const PROPERTY_BLOG_ID = 'blog_id';
	const PROPERTY_BLOG_NAME = 'blog_name';
	const PROPERTY_BLOG_SUBTITLE = 'blog_subtitle';
	const PROPERTY_DATE_CREATION = 'date_creation';
	const PROPERTY_VISIBILITY = 'visibility';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185Blog object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Blog($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_BLOG_ID, SELF :: PROPERTY_BLOG_NAME, SELF :: PROPERTY_BLOG_SUBTITLE, SELF :: PROPERTY_DATE_CREATION, SELF :: PROPERTY_VISIBILITY);
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
	 * Returns the blog_id of this Dokeos185Blog.
	 * @return the blog_id.
	 */
	function get_blog_id()
	{
		return $this->get_default_property(self :: PROPERTY_BLOG_ID);
	}

	/**
	 * Sets the blog_id of this Dokeos185Blog.
	 * @param blog_id
	 */
	function set_blog_id($blog_id)
	{
		$this->set_default_property(self :: PROPERTY_BLOG_ID, $blog_id);
	}
	/**
	 * Returns the blog_name of this Dokeos185Blog.
	 * @return the blog_name.
	 */
	function get_blog_name()
	{
		return $this->get_default_property(self :: PROPERTY_BLOG_NAME);
	}

	/**
	 * Sets the blog_name of this Dokeos185Blog.
	 * @param blog_name
	 */
	function set_blog_name($blog_name)
	{
		$this->set_default_property(self :: PROPERTY_BLOG_NAME, $blog_name);
	}
	/**
	 * Returns the blog_subtitle of this Dokeos185Blog.
	 * @return the blog_subtitle.
	 */
	function get_blog_subtitle()
	{
		return $this->get_default_property(self :: PROPERTY_BLOG_SUBTITLE);
	}

	/**
	 * Sets the blog_subtitle of this Dokeos185Blog.
	 * @param blog_subtitle
	 */
	function set_blog_subtitle($blog_subtitle)
	{
		$this->set_default_property(self :: PROPERTY_BLOG_SUBTITLE, $blog_subtitle);
	}
	/**
	 * Returns the date_creation of this Dokeos185Blog.
	 * @return the date_creation.
	 */
	function get_date_creation()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_CREATION);
	}

	/**
	 * Sets the date_creation of this Dokeos185Blog.
	 * @param date_creation
	 */
	function set_date_creation($date_creation)
	{
		$this->set_default_property(self :: PROPERTY_DATE_CREATION, $date_creation);
	}
	/**
	 * Returns the visibility of this Dokeos185Blog.
	 * @return the visibility.
	 */
	function get_visibility()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBILITY);
	}

	/**
	 * Sets the visibility of this Dokeos185Blog.
	 * @param visibility
	 */
	function set_visibility($visibility)
	{
		$this->set_default_property(self :: PROPERTY_VISIBILITY, $visibility);
	}

}

?>