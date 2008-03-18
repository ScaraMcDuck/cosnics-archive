<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 forum_thread
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumThread
{
	/**
	 * Dokeos185ForumThread properties
	 */
	const PROPERTY_THREAD_ID = 'thread_id';
	const PROPERTY_THREAD_TITLE = 'thread_title';
	const PROPERTY_FORUM_ID = 'forum_id';
	const PROPERTY_THREAD_REPLIES = 'thread_replies';
	const PROPERTY_THREAD_POSTER_ID = 'thread_poster_id';
	const PROPERTY_THREAD_POSTER_NAME = 'thread_poster_name';
	const PROPERTY_THREAD_VIEWS = 'thread_views';
	const PROPERTY_THREAD_LAST_POST = 'thread_last_post';
	const PROPERTY_THREAD_DATE = 'thread_date';
	const PROPERTY_THREAD_STICKY = 'thread_sticky';
	const PROPERTY_LOCKED = 'locked';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185ForumThread object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185ForumThread($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_THREAD_ID, SELF :: PROPERTY_THREAD_TITLE, SELF :: PROPERTY_FORUM_ID, SELF :: PROPERTY_THREAD_REPLIES, SELF :: PROPERTY_THREAD_POSTER_ID, SELF :: PROPERTY_THREAD_POSTER_NAME, SELF :: PROPERTY_THREAD_VIEWS, SELF :: PROPERTY_THREAD_LAST_POST, SELF :: PROPERTY_THREAD_DATE, SELF :: PROPERTY_THREAD_STICKY, SELF :: PROPERTY_LOCKED);
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
	 * Returns the thread_id of this Dokeos185ForumThread.
	 * @return the thread_id.
	 */
	function get_thread_id()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_ID);
	}

	/**
	 * Returns the thread_title of this Dokeos185ForumThread.
	 * @return the thread_title.
	 */
	function get_thread_title()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_TITLE);
	}

	/**
	 * Returns the forum_id of this Dokeos185ForumThread.
	 * @return the forum_id.
	 */
	function get_forum_id()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ID);
	}

	/**
	 * Returns the thread_replies of this Dokeos185ForumThread.
	 * @return the thread_replies.
	 */
	function get_thread_replies()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_REPLIES);
	}

	/**
	 * Returns the thread_poster_id of this Dokeos185ForumThread.
	 * @return the thread_poster_id.
	 */
	function get_thread_poster_id()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_POSTER_ID);
	}

	/**
	 * Returns the thread_poster_name of this Dokeos185ForumThread.
	 * @return the thread_poster_name.
	 */
	function get_thread_poster_name()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_POSTER_NAME);
	}

	/**
	 * Returns the thread_views of this Dokeos185ForumThread.
	 * @return the thread_views.
	 */
	function get_thread_views()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_VIEWS);
	}

	/**
	 * Returns the thread_last_post of this Dokeos185ForumThread.
	 * @return the thread_last_post.
	 */
	function get_thread_last_post()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_LAST_POST);
	}

	/**
	 * Returns the thread_date of this Dokeos185ForumThread.
	 * @return the thread_date.
	 */
	function get_thread_date()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_DATE);
	}

	/**
	 * Returns the thread_sticky of this Dokeos185ForumThread.
	 * @return the thread_sticky.
	 */
	function get_thread_sticky()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_STICKY);
	}

	/**
	 * Returns the locked of this Dokeos185ForumThread.
	 * @return the locked.
	 */
	function get_locked()
	{
		return $this->get_default_property(self :: PROPERTY_LOCKED);
	}


}

?>