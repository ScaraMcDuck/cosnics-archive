<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 student_publication
 *
 * @author Sven Vanpoucke
 */
class Dokeos185StudentPublication
{
	/**
	 * Dokeos185StudentPublication properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_URL = 'url';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_AUTHOR = 'author';
	const PROPERTY_ACTIVE = 'active';
	const PROPERTY_ACCEPTED = 'accepted';
	const PROPERTY_POST_GROUP_ID = 'post_group_id';
	const PROPERTY_SENT_DATE = 'sent_date';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185StudentPublication object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185StudentPublication($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_URL, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_DESCRIPTION, SELF :: PROPERTY_AUTHOR, SELF :: PROPERTY_ACTIVE, SELF :: PROPERTY_ACCEPTED, SELF :: PROPERTY_POST_GROUP_ID, SELF :: PROPERTY_SENT_DATE);
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
	 * Returns the id of this Dokeos185StudentPublication.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185StudentPublication.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the url of this Dokeos185StudentPublication.
	 * @return the url.
	 */
	function get_url()
	{
		return $this->get_default_property(self :: PROPERTY_URL);
	}

	/**
	 * Sets the url of this Dokeos185StudentPublication.
	 * @param url
	 */
	function set_url($url)
	{
		$this->set_default_property(self :: PROPERTY_URL, $url);
	}
	/**
	 * Returns the title of this Dokeos185StudentPublication.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Sets the title of this Dokeos185StudentPublication.
	 * @param title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	/**
	 * Returns the description of this Dokeos185StudentPublication.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Sets the description of this Dokeos185StudentPublication.
	 * @param description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	/**
	 * Returns the author of this Dokeos185StudentPublication.
	 * @return the author.
	 */
	function get_author()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR);
	}

	/**
	 * Sets the author of this Dokeos185StudentPublication.
	 * @param author
	 */
	function set_author($author)
	{
		$this->set_default_property(self :: PROPERTY_AUTHOR, $author);
	}
	/**
	 * Returns the active of this Dokeos185StudentPublication.
	 * @return the active.
	 */
	function get_active()
	{
		return $this->get_default_property(self :: PROPERTY_ACTIVE);
	}

	/**
	 * Sets the active of this Dokeos185StudentPublication.
	 * @param active
	 */
	function set_active($active)
	{
		$this->set_default_property(self :: PROPERTY_ACTIVE, $active);
	}
	/**
	 * Returns the accepted of this Dokeos185StudentPublication.
	 * @return the accepted.
	 */
	function get_accepted()
	{
		return $this->get_default_property(self :: PROPERTY_ACCEPTED);
	}

	/**
	 * Sets the accepted of this Dokeos185StudentPublication.
	 * @param accepted
	 */
	function set_accepted($accepted)
	{
		$this->set_default_property(self :: PROPERTY_ACCEPTED, $accepted);
	}
	/**
	 * Returns the post_group_id of this Dokeos185StudentPublication.
	 * @return the post_group_id.
	 */
	function get_post_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_POST_GROUP_ID);
	}

	/**
	 * Sets the post_group_id of this Dokeos185StudentPublication.
	 * @param post_group_id
	 */
	function set_post_group_id($post_group_id)
	{
		$this->set_default_property(self :: PROPERTY_POST_GROUP_ID, $post_group_id);
	}
	/**
	 * Returns the sent_date of this Dokeos185StudentPublication.
	 * @return the sent_date.
	 */
	function get_sent_date()
	{
		return $this->get_default_property(self :: PROPERTY_SENT_DATE);
	}

	/**
	 * Sets the sent_date of this Dokeos185StudentPublication.
	 * @param sent_date
	 */
	function set_sent_date($sent_date)
	{
		$this->set_default_property(self :: PROPERTY_SENT_DATE, $sent_date);
	}

}

?>