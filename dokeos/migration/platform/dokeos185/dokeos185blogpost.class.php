<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 blog_post
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogPost
{
	/**
	 * Dokeos185BlogPost properties
	 */
	const PROPERTY_POST_ID = 'post_id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_FULL_TEXT = 'full_text';
	const PROPERTY_DATE_CREATION = 'date_creation';
	const PROPERTY_BLOG_ID = 'blog_id';
	const PROPERTY_AUTHOR_ID = 'author_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185BlogPost object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185BlogPost($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_POST_ID, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_FULL_TEXT, SELF :: PROPERTY_DATE_CREATION, SELF :: PROPERTY_BLOG_ID, SELF :: PROPERTY_AUTHOR_ID);
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
	 * Returns the post_id of this Dokeos185BlogPost.
	 * @return the post_id.
	 */
	function get_post_id()
	{
		return $this->get_default_property(self :: PROPERTY_POST_ID);
	}

	/**
	 * Sets the post_id of this Dokeos185BlogPost.
	 * @param post_id
	 */
	function set_post_id($post_id)
	{
		$this->set_default_property(self :: PROPERTY_POST_ID, $post_id);
	}
	/**
	 * Returns the title of this Dokeos185BlogPost.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Sets the title of this Dokeos185BlogPost.
	 * @param title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	/**
	 * Returns the full_text of this Dokeos185BlogPost.
	 * @return the full_text.
	 */
	function get_full_text()
	{
		return $this->get_default_property(self :: PROPERTY_FULL_TEXT);
	}

	/**
	 * Sets the full_text of this Dokeos185BlogPost.
	 * @param full_text
	 */
	function set_full_text($full_text)
	{
		$this->set_default_property(self :: PROPERTY_FULL_TEXT, $full_text);
	}
	/**
	 * Returns the date_creation of this Dokeos185BlogPost.
	 * @return the date_creation.
	 */
	function get_date_creation()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_CREATION);
	}

	/**
	 * Sets the date_creation of this Dokeos185BlogPost.
	 * @param date_creation
	 */
	function set_date_creation($date_creation)
	{
		$this->set_default_property(self :: PROPERTY_DATE_CREATION, $date_creation);
	}
	/**
	 * Returns the blog_id of this Dokeos185BlogPost.
	 * @return the blog_id.
	 */
	function get_blog_id()
	{
		return $this->get_default_property(self :: PROPERTY_BLOG_ID);
	}

	/**
	 * Sets the blog_id of this Dokeos185BlogPost.
	 * @param blog_id
	 */
	function set_blog_id($blog_id)
	{
		$this->set_default_property(self :: PROPERTY_BLOG_ID, $blog_id);
	}
	/**
	 * Returns the author_id of this Dokeos185BlogPost.
	 * @return the author_id.
	 */
	function get_author_id()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR_ID);
	}

	/**
	 * Sets the author_id of this Dokeos185BlogPost.
	 * @param author_id
	 */
	function set_author_id($author_id)
	{
		$this->set_default_property(self :: PROPERTY_AUTHOR_ID, $author_id);
	}

}

?>