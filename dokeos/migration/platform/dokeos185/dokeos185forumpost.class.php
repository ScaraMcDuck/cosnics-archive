<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 forum_post
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumPost
{
	/**
	 * Dokeos185ForumPost properties
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
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185ForumPost object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185ForumPost($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_POST_ID, SELF :: PROPERTY_POST_TITLE, SELF :: PROPERTY_POST_TEXT, SELF :: PROPERTY_THREAD_ID, SELF :: PROPERTY_FORUM_ID, SELF :: PROPERTY_POSTER_ID, SELF :: PROPERTY_POSTER_NAME, SELF :: PROPERTY_POST_DATE, SELF :: PROPERTY_POST_NOTIFICATION, SELF :: PROPERTY_POST_PARENT_ID, SELF :: PROPERTY_VISIBLE);
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
	 * Returns the post_id of this Dokeos185ForumPost.
	 * @return the post_id.
	 */
	function get_post_id()
	{
		return $this->get_default_property(self :: PROPERTY_POST_ID);
	}

	/**
	 * Returns the post_title of this Dokeos185ForumPost.
	 * @return the post_title.
	 */
	function get_post_title()
	{
		return $this->get_default_property(self :: PROPERTY_POST_TITLE);
	}

	/**
	 * Returns the post_text of this Dokeos185ForumPost.
	 * @return the post_text.
	 */
	function get_post_text()
	{
		return $this->get_default_property(self :: PROPERTY_POST_TEXT);
	}

	/**
	 * Returns the thread_id of this Dokeos185ForumPost.
	 * @return the thread_id.
	 */
	function get_thread_id()
	{
		return $this->get_default_property(self :: PROPERTY_THREAD_ID);
	}

	/**
	 * Returns the forum_id of this Dokeos185ForumPost.
	 * @return the forum_id.
	 */
	function get_forum_id()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ID);
	}

	/**
	 * Returns the poster_id of this Dokeos185ForumPost.
	 * @return the poster_id.
	 */
	function get_poster_id()
	{
		return $this->get_default_property(self :: PROPERTY_POSTER_ID);
	}

	/**
	 * Returns the poster_name of this Dokeos185ForumPost.
	 * @return the poster_name.
	 */
	function get_poster_name()
	{
		return $this->get_default_property(self :: PROPERTY_POSTER_NAME);
	}

	/**
	 * Returns the post_date of this Dokeos185ForumPost.
	 * @return the post_date.
	 */
	function get_post_date()
	{
		return $this->get_default_property(self :: PROPERTY_POST_DATE);
	}

	/**
	 * Returns the post_notification of this Dokeos185ForumPost.
	 * @return the post_notification.
	 */
	function get_post_notification()
	{
		return $this->get_default_property(self :: PROPERTY_POST_NOTIFICATION);
	}

	/**
	 * Returns the post_parent_id of this Dokeos185ForumPost.
	 * @return the post_parent_id.
	 */
	function get_post_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_POST_PARENT_ID);
	}

	/**
	 * Returns the visible of this Dokeos185ForumPost.
	 * @return the visible.
	 */
	function get_visible()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE);
	}
	
	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];
		
		if($array['del_files'] =! 1)
			$tool_name = 'forum_post';
		
		$coursedb = $array['course'];
		$tablename = 'forum_post';
		$classname = 'Dokeos185ForumPost';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}


}

?>