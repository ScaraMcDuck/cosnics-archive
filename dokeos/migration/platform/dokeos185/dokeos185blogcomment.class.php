<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 blog_comment
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogComment
{
	/**
	 * Dokeos185BlogComment properties
	 */
	const PROPERTY_COMMENT_ID = 'comment_id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_COMMENT = 'comment';
	const PROPERTY_AUTHOR_ID = 'author_id';
	const PROPERTY_DATE_CREATION = 'date_creation';
	const PROPERTY_BLOG_ID = 'blog_id';
	const PROPERTY_POST_ID = 'post_id';
	const PROPERTY_TASK_ID = 'task_id';
	const PROPERTY_PARENT_COMMENT_ID = 'parent_comment_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185BlogComment object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185BlogComment($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_COMMENT_ID, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_COMMENT, SELF :: PROPERTY_AUTHOR_ID, SELF :: PROPERTY_DATE_CREATION, SELF :: PROPERTY_BLOG_ID, SELF :: PROPERTY_POST_ID, SELF :: PROPERTY_TASK_ID, SELF :: PROPERTY_PARENT_COMMENT_ID);
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
	 * Returns the comment_id of this Dokeos185BlogComment.
	 * @return the comment_id.
	 */
	function get_comment_id()
	{
		return $this->get_default_property(self :: PROPERTY_COMMENT_ID);
	}

	/**
	 * Returns the title of this Dokeos185BlogComment.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Returns the comment of this Dokeos185BlogComment.
	 * @return the comment.
	 */
	function get_comment()
	{
		return $this->get_default_property(self :: PROPERTY_COMMENT);
	}

	/**
	 * Returns the author_id of this Dokeos185BlogComment.
	 * @return the author_id.
	 */
	function get_author_id()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR_ID);
	}

	/**
	 * Returns the date_creation of this Dokeos185BlogComment.
	 * @return the date_creation.
	 */
	function get_date_creation()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_CREATION);
	}

	/**
	 * Returns the blog_id of this Dokeos185BlogComment.
	 * @return the blog_id.
	 */
	function get_blog_id()
	{
		return $this->get_default_property(self :: PROPERTY_BLOG_ID);
	}

	/**
	 * Returns the post_id of this Dokeos185BlogComment.
	 * @return the post_id.
	 */
	function get_post_id()
	{
		return $this->get_default_property(self :: PROPERTY_POST_ID);
	}

	/**
	 * Returns the task_id of this Dokeos185BlogComment.
	 * @return the task_id.
	 */
	function get_task_id()
	{
		return $this->get_default_property(self :: PROPERTY_TASK_ID);
	}

	/**
	 * Returns the parent_comment_id of this Dokeos185BlogComment.
	 * @return the parent_comment_id.
	 */
	function get_parent_comment_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_COMMENT_ID);
	}


}

?>