<?php
/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcourse.class.php';
require_once dirname(__FILE__).'/../../../application/lib/weblcms/course/course.class.php';

/**
 * This class represents an old Dokeos 1.8.5 forum thread
 *
 * @author David Van Wayenbergh
 */
 
 
class dokeos185forumthread
{

    /**
	 * Migration data manager
	 */
	private static $mgdm;
	
	/**
	 * forum thread properties
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
	 * Default properties of the forum thread object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new forum thread object.
	 * @param array $defaultProperties The default properties of the forum thread
	 *                                 object. Associative array.
	 */
	function Dokeos185ForumThread($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this forum thread object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this forum thread.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all forum threads.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_CAT_ID,self::PROPERTY_CAT_TITLE,self::PROPERTY_CAT_COMMENT,
		self::PROPERTY_LOCKED,self::PROPERTY_CAT_ORDER);
	}
	
	/**
	 * Sets a default property of this forum thread by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Sets the default properties of this forum thread
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Checks if the given identifier is the name of a default forum thread
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
	 * Returns the thread_id of this forum thread.
	 * @return int The thread_id.
	 */
	function get_thread_id()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_ID);
	}
	
	/**
	 * Returns the thread_title of this forum thread.
	 * @return int The thread_title.
	 */
	function get_thread_title()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_TITLE);
	}
	
	/**
	 * Returns the forum_id of this forum thread.
	 * @return int The forum_id.
	 */
	function get_forum_id()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ID);
	}
	
	/**
	 * Returns the thread_replies of this forum thread.
	 * @return int The thread_replies.
	 */
	function get_thread_replies()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_REPLIES);
	}
	
	/**
	 * Returns the thread_poster_id of this forum thread.
	 * @return int The thread_poster_id.
	 */
	function get_thread_poster_id()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_POSTER_ID);
	}
	
	/**
	 * Returns the thread_poster_name of this forum thread.
	 * @return int The thread_poster_name.
	 */
	function get_thread_poster_name()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_POSTER_NAME);
	}
	
	/**
	 * Returns the thread_views of this forum thread.
	 * @return int The thread_views.
	 */
	function get_thread_views()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_VIEWS);
	}
	
	/**
	 * Returns the thread_last_post of this forum thread.
	 * @return int The thread_last_post.
	 */
	function get_thread_last_post()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_LAST_POST);
	}
	
	/**
	 * Returns the thread_date of this forum thread.
	 * @return int The thread_date.
	 */
	function get_thread_date()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_DATE);
	}
	
	/**
	 * Returns the thread_sticky of this forum thread.
	 * @return int The thread_sticky.
	 */
	function get_thread_sticky()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_STICKY);
	}
	
	/**
	 * Returns the locked of this forum thread.
	 * @return int The locked.
	 */
	function get_locked()
	{
		return $this->get_default_property(self :: PROPERTY_LOCKED);
	}
}
?>