<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcourse.class.php';
require_once dirname(__FILE__).'/../../../application/lib/weblcms/course/course.class.php';

/**
 * This class represents an old Dokeos 1.8.5 forum forum
 *
 * @author David Van Wayenbergh
 */
 
 
class dokeos185forumforum 
{

    /**
	 * Migration data manager
	 */
	private static $mgdm;
	
	/**
	 * forum forum properties
	 */
	const PROPERTY_FORUM_ID = 'forum_id';
	const PROPERTY_FORUM_TITLE = 'forum_title';
	const PROPERTY_FORUM_COMMENT = 'forum_comment';
	const PROPERTY_FORUM_THREADS = 'forum_threads';
	const PROPERTY_FORUM_POSTS = 'forum_posts';
	const PROPERTY_FORUM_LAST_POST = 'forum_last_post';
	const PROPERTY_FORUM_CATEGORY = 'forum_category';
	const PROPERTY_LOCKED = 'locked';
	const PROPERTY_ALLOW_ANONYMOUS = 'allow_anonymous';
	const PROPERTY_ALLOW_EDIT = 'allow_edit';
	const PROPERTY_APPROVAL_DIRECT_POST = 'approval_direct_post';
	const PROPERTY_ALLOW_ATTACHMENTS = 'allow_attachments';
	const PROPERTY_ALLOW_NEW_THREADS = 'allow_new_threads';
	const PROPERTY_DEFAULT_VIEW = 'default_view';
	const PROPERTY_FORUM_OF_GROUP = 'forum_of_group';
	const PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE = 'forum_group_public_private';
	const PROPERTY_FORUM_ORDER = 'forum_order';
	
	/**
	 * Alfanumeric identifier of the course object.
	 */
	private $code;
	
	/**
	 * Default properties of the forum forum object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new forum forum object.
	 * @param array $defaultProperties The default properties of the forum forum
	 *                                 object. Associative array.
	 */
	function Dokeos185ForumForum($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this forum forum object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this forum forum.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all forum forums.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_FORUM_ID,self::PROPERTY_FORUM_TITLE,self::PROPERTY_FORUM_COMMENT,
		self::PROPERTY_LOCKED,self::PROPERTY_FORUM_THREADS,self::PROPERTY_FORUM_POSTS,
		self::PROPERTY_FORUM_LAST_POSTS,self::PROPERTY_CATEGORY,self::PROPERTY_ALLOW_ANONYMOUS,
		self::PROPERTY_ALLOW_EDIT,self::PROPERTY_APPROVAL_DIRECT_POST,self::PROPERTY_ALLOW_ATTACHMENTS,
		self::PROPERTY_ALLOW_NEW_THREADS,self::PROPERTY_DEFAULT_VIEW,self::PROPERTY_FORUM_OF_GROUP,
		self::PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE,self::PROPERTY_FORUM_ORDER);
	}
	
	/**
	 * Sets a default property of this forum forum by name.
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
	 * Checks if the given identifier is the name of a default forum forum
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
	 * Returns the forum_id of this forum forum.
	 * @return int The forum_id.
	 */
	function get_forum_id()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ID);
	}
	
	/**
	 * Returns the forum_title of this forum forum.
	 * @return int The forum_title.
	 */
	function get_forum_title()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_TITLE);
	}
	
	/**
	 * Returns the forum_comment of this forum forum.
	 * @return int The forum_comment.
	 */
	function get_forum_comment()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_COMMENT);
	}
	
	/**
	 * Returns the locked of this forum forum.
	 * @return int The locked.
	 */
	function get_locked()
	{
		return $this->get_default_property(self :: PROPERTY_LOCKED);
	}
	
	/**
	 * Returns the forum_threads of this forum forum.
	 * @return int The forum_threads.
	 */
	function get_forum_threads()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_THREADS);
	}
	
	/**
	 * Returns the forum_posts of this forum forum.
	 * @return int The forum_posts.
	 */
	function get_forum_posts()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_POSTS);
	}
	
	/**
	 * Returns the forum_last_post of this forum forum.
	 * @return int The forum_last_post.
	 */
	function get_forum_last_post()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_LAST_POST);
	}
	
	/**
	 * Returns the forum_category of this forum forum.
	 * @return int The forum_category.
	 */
	function get_forum_category()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_CATEGORY);
	}
	
	/**
	 * Returns the allow_anonymous of this forum forum.
	 * @return int The allow_anonymous.
	 */
	function get_allow_anonymous()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_ANONYMOUS);
	}
	
	/**
	 * Returns the forum_id of this forum forum.
	 * @return int The forum_id.
	 */
	function get_forum_id()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ID);
	}
	
	/**
	 * Returns the allow_edit of this forum forum.
	 * @return int The allow_edit.
	 */
	function get_allow_edit()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_EDIT);
	}
	
	/**
	 * Returns the approval_direct_post of this forum forum.
	 * @return int The approval_direct_post.
	 */
	function get_approval_direct_post()
	{
		return $this->get_default_property(self :: PROPERTY_APPROVAL_DIRECT_POST);
	}
	
	/**
	 * Returns the allow_attachments of this forum forum.
	 * @return int The allow_attachments.
	 */
	function get_allow_attachments()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_ATTACHMENTS);
	}
	
	/**
	 * Returns the allow_new_threads of this forum forum.
	 * @return int The allow_new_threads.
	 */
	function get_allow_new_threads()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_NEW_THREADS);
	}
	
	/**
	 * Returns the default_view of this forum forum.
	 * @return int The default_view.
	 */
	function get_default_view()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_VIEW);
	}
	
	/**
	 * Returns the forum_of_group of this forum forum.
	 * @return int The forum_of_group.
	 */
	function get_forum_of_group()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_OF_GROUP);
	}
	
	/**
	 * Returns the forum_group_public_private of this forum forum.
	 * @return int The forum_group_public_private.
	 */
	function get_forum_group_public_private()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE);
	}
	
	/**
	 * Returns the forum_order of this forum forum.
	 * @return int The forum_order.
	 */
	function get_forum_order()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ORDER);
	}
	
	
}
?>