<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcourse.class.php';
require_once dirname(__FILE__).'/../../../application/lib/weblcms/course/course.class.php';

/**
 * This class represents an old Dokeos 1.8.5 forum post
 *
 * @author David Van Wayenbergh
 */
 
 
class dokeos185forumpost
{

    /**
	 * Migration data manager
	 */
	private static $mgdm;
	
	/**
	 * forum forum properties
	 */
	const PROPERTY_POST_ID = 'post_id';
	const PROPERTY_POST_TITLE = 'post_title';
	const PROPERTY_POST_TEXT = 'post_text';
	const PROPERTY_THREAD_ID = 'thread_id';
	const PROPERTY_FORUM_ID = 'forum_id';
	const PROPERTY_POSTER_ID = 'poster_id';
	const PROPERTY_POSTER_NAME = 'poster_name';
	const PROPERTY_POST_DATE = 'post_date';
	const PROPERTY_POST_NOTIFICATION = 'post_notification';
	const PROPERTY_POST_PARENT_ID = 'post_parent_id';
	const PROPERTY_VISIBLE = 'visible';
	
	/**
	 * Default properties of the forum post object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new forum post object.
	 * @param array $defaultProperties The default properties of the forum post
	 *                                 object. Associative array.
	 */
	function Dokeos185ForumPost($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this forum post object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this forum post.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all forum posts.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_POST_ID,self::PROPERTY_POST_TITLE,self::PROPERTY_POST_TEXT,
		self::PROPERTY_THREAD_ID,self::PROPERTY_FORUM_ID,self::PROPERTY_POSTER_ID,
		self::PROPERTY_POSTER_NAME,self::PROPERTY_POST_DATE,self::PROPERTY_POST_NOTIFICATION,
		self::PROPERTY_POST_PARENT_ID,self::PROPERTY_VISIBLE);
	}
	
	/**
	 * Sets a default property of this forum post by name.
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
	 * Checks if the given identifier is the name of a default forum post
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	/**
	 * Returns the post_id of this forum post.
	 * @return int The post_id.
	 */
	function get_post_id()
	{
		return $this->get_default_property(self :: PROPERTY_POST_ID);
	}
	
	/**
	 * Returns the post_title of this forum post.
	 * @return int The post_title.
	 */
	function get_post_title()
	{
		return $this->get_default_property(self :: PROPERTY_POST_TITLE);
	}
	
	/**
	 * Returns the post_text of this forum post.
	 * @return int The post_text.
	 */
	function get_post_text()
	{
		return $this->get_default_property(self :: PROPERTY_POST_TEXT);
	}
	
	/**
	 * Returns the thread_id of this forum post.
	 * @return int The thread_id.
	 */
	function get_thread_id()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_ID);
	}
	
	/**
	 * Returns the forum_id of this forum post.
	 * @return int The forum_id.
	 */
	function get_forum_id()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ID);
	}
	
	/**
	 * Returns the poster_id of this forum post.
	 * @return int The poster_id.
	 */
	function get_poster_id()
	{
		return $this->get_default_property(self :: PROPERTY_POSTER_ID);
	}
	
	/**
	 * Returns the poster_name of this forum post.
	 * @return int The poster_name.
	 */
	function get_poster_name()
	{
		return $this->get_default_property(self :: PROPERTY_POSTER_NAME);
	}
	
	/**
	 * Returns the post_date of this forum post.
	 * @return int The post_date.
	 */
	function get_post_date()
	{
		return $this->get_default_property(self :: PROPERTY_POST_DATE);
	}
	
	/**
	 * Returns the post_notification of this forum post.
	 * @return int The post_notification.
	 */
	function get_post_notification()
	{
		return $this->get_default_property(self :: PROPERTY_POST_NOTIFICATION);
	}
	
	/**
	 * Returns the post_parent_id of this forum post.
	 * @return int The post_parent_id.
	 */
	function get_post_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_POST_PARENT_ID);
	}
	
	/**
	 * Returns the visible of this forum post.
	 * @return int The visible.
	 */
	function get_visible()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE);
	}
}
?>