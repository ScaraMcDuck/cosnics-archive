<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 forum_forum
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumForum
{
	/**
	 * Dokeos185ForumForum properties
	 */
	const PROPERTY_FORUM_ID = 'forum_id';
	const PROPERTY_FORUM_TITLE = 'forum_title';
	const PROPERTY_FORUM_COMMENT = 'forum_comment';
	const PROPERTY_FORUM_THREADS = 'forum_threads';
	const PROPERTY_FORUM_POSTS = 'forum_posts';
	const PROPERTY_FORUM_LAST_POST = 'forum_last_post';
	const PROPERTY_FORUM_CATEGORY = 'forum_category';
	const PROPERTY_ALLOW_ANONYMOUS = 'allow_anonymous';
	const PROPERTY_ALLOW_EDIT = 'allow_edit';
	const PROPERTY_APPROVAL_DIRECT_POST = 'approval_direct_post';
	const PROPERTY_ALLOW_ATTACHMENTS = 'allow_attachments';
	const PROPERTY_ALLOW_NEW_THREADS = 'allow_new_threads';
	const PROPERTY_DEFAULT_VIEW = 'default_view';
	const PROPERTY_FORUM_OF_GROUP = 'forum_of_group';
	const PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE = 'forum_group_public_private';
	const PROPERTY_FORUM_ORDER = 'forum_order';
	const PROPERTY_LOCKED = 'locked';
	const PROPERTY_SESSION_ID = 'session_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185ForumForum object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185ForumForum($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_FORUM_ID, SELF :: PROPERTY_FORUM_TITLE, SELF :: PROPERTY_FORUM_COMMENT, SELF :: PROPERTY_FORUM_THREADS, SELF :: PROPERTY_FORUM_POSTS, SELF :: PROPERTY_FORUM_LAST_POST, SELF :: PROPERTY_FORUM_CATEGORY, SELF :: PROPERTY_ALLOW_ANONYMOUS, SELF :: PROPERTY_ALLOW_EDIT, SELF :: PROPERTY_APPROVAL_DIRECT_POST, SELF :: PROPERTY_ALLOW_ATTACHMENTS, SELF :: PROPERTY_ALLOW_NEW_THREADS, SELF :: PROPERTY_DEFAULT_VIEW, SELF :: PROPERTY_FORUM_OF_GROUP, SELF :: PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE, SELF :: PROPERTY_FORUM_ORDER, SELF :: PROPERTY_LOCKED, SELF :: PROPERTY_SESSION_ID);
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
	 * Returns the forum_id of this Dokeos185ForumForum.
	 * @return the forum_id.
	 */
	function get_forum_id()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ID);
	}

	/**
	 * Returns the forum_title of this Dokeos185ForumForum.
	 * @return the forum_title.
	 */
	function get_forum_title()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_TITLE);
	}

	/**
	 * Returns the forum_comment of this Dokeos185ForumForum.
	 * @return the forum_comment.
	 */
	function get_forum_comment()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_COMMENT);
	}

	/**
	 * Returns the forum_threads of this Dokeos185ForumForum.
	 * @return the forum_threads.
	 */
	function get_forum_threads()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_THREADS);
	}

	/**
	 * Returns the forum_posts of this Dokeos185ForumForum.
	 * @return the forum_posts.
	 */
	function get_forum_posts()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_POSTS);
	}

	/**
	 * Returns the forum_last_post of this Dokeos185ForumForum.
	 * @return the forum_last_post.
	 */
	function get_forum_last_post()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_LAST_POST);
	}

	/**
	 * Returns the forum_category of this Dokeos185ForumForum.
	 * @return the forum_category.
	 */
	function get_forum_category()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_CATEGORY);
	}

	/**
	 * Returns the allow_anonymous of this Dokeos185ForumForum.
	 * @return the allow_anonymous.
	 */
	function get_allow_anonymous()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_ANONYMOUS);
	}

	/**
	 * Returns the allow_edit of this Dokeos185ForumForum.
	 * @return the allow_edit.
	 */
	function get_allow_edit()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_EDIT);
	}

	/**
	 * Returns the approval_direct_post of this Dokeos185ForumForum.
	 * @return the approval_direct_post.
	 */
	function get_approval_direct_post()
	{
		return $this->get_default_property(self :: PROPERTY_APPROVAL_DIRECT_POST);
	}

	/**
	 * Returns the allow_attachments of this Dokeos185ForumForum.
	 * @return the allow_attachments.
	 */
	function get_allow_attachments()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_ATTACHMENTS);
	}

	/**
	 * Returns the allow_new_threads of this Dokeos185ForumForum.
	 * @return the allow_new_threads.
	 */
	function get_allow_new_threads()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_NEW_THREADS);
	}

	/**
	 * Returns the default_view of this Dokeos185ForumForum.
	 * @return the default_view.
	 */
	function get_default_view()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_VIEW);
	}

	/**
	 * Returns the forum_of_group of this Dokeos185ForumForum.
	 * @return the forum_of_group.
	 */
	function get_forum_of_group()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_OF_GROUP);
	}

	/**
	 * Returns the forum_group_public_private of this Dokeos185ForumForum.
	 * @return the forum_group_public_private.
	 */
	function get_forum_group_public_private()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE);
	}

	/**
	 * Returns the forum_order of this Dokeos185ForumForum.
	 * @return the forum_order.
	 */
	function get_forum_order()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ORDER);
	}

	/**
	 * Returns the locked of this Dokeos185ForumForum.
	 * @return the locked.
	 */
	function get_locked()
	{
		return $this->get_default_property(self :: PROPERTY_LOCKED);
	}

	/**
	 * Returns the session_id of this Dokeos185ForumForum.
	 * @return the session_id.
	 */
	function get_session_id()
	{
		return $this->get_default_property(self :: PROPERTY_SESSION_ID);
	}


}

?>