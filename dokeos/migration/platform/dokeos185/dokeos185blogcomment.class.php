<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 blog_comment
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
	 * Sets the comment_id of this Dokeos185BlogComment.
	 * @param comment_id
	 */
	function set_comment_id($comment_id)
	{
		$this->set_default_property(self :: PROPERTY_COMMENT_ID, $comment_id);
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
	 * Sets the title of this Dokeos185BlogComment.
	 * @param title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
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
	 * Sets the comment of this Dokeos185BlogComment.
	 * @param comment
	 */
	function set_comment($comment)
	{
		$this->set_default_property(self :: PROPERTY_COMMENT, $comment);
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
	 * Sets the author_id of this Dokeos185BlogComment.
	 * @param author_id
	 */
	function set_author_id($author_id)
	{
		$this->set_default_property(self :: PROPERTY_AUTHOR_ID, $author_id);
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
	 * Sets the date_creation of this Dokeos185BlogComment.
	 * @param date_creation
	 */
	function set_date_creation($date_creation)
	{
		$this->set_default_property(self :: PROPERTY_DATE_CREATION, $date_creation);
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
	 * Sets the blog_id of this Dokeos185BlogComment.
	 * @param blog_id
	 */
	function set_blog_id($blog_id)
	{
		$this->set_default_property(self :: PROPERTY_BLOG_ID, $blog_id);
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
	 * Sets the post_id of this Dokeos185BlogComment.
	 * @param post_id
	 */
	function set_post_id($post_id)
	{
		$this->set_default_property(self :: PROPERTY_POST_ID, $post_id);
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
	 * Sets the task_id of this Dokeos185BlogComment.
	 * @param task_id
	 */
	function set_task_id($task_id)
	{
		$this->set_default_property(self :: PROPERTY_TASK_ID, $task_id);
	}
	/**
	 * Returns the parent_comment_id of this Dokeos185BlogComment.
	 * @return the parent_comment_id.
	 */
	function get_parent_comment_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_COMMENT_ID);
	}

	/**
	 * Sets the parent_comment_id of this Dokeos185BlogComment.
	 * @param parent_comment_id
	 */
	function set_parent_comment_id($parent_comment_id)
	{
		$this->set_default_property(self :: PROPERTY_PARENT_COMMENT_ID, $parent_comment_id);
	}

}

?>