<?php 
/**
 * forum
 */

/**
 * This class describes a ForumPublication data object
 *
 * @author Sven Vanpoucke & Michael Kyndt
 */
class ForumPublication
{
	const CLASS_NAME = __CLASS__;

	/**
	 * ForumPublication properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_FORUM_ID = 'forum_id';
	const PROPERTY_AUTHOR = 'author';
	const PROPERTY_DATE = 'date';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new ForumPublication object
	 * @param array $defaultProperties The default properties
	 */
	function ForumPublication($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_FORUM_ID, self :: PROPERTY_AUTHOR, self :: PROPERTY_DATE);
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
	 * Returns the id of this ForumPublication.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this ForumPublication.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the forum_id of this ForumPublication.
	 * @return the forum_id.
	 */
	function get_forum_id()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ID);
	}

	/**
	 * Sets the forum_id of this ForumPublication.
	 * @param forum_id
	 */
	function set_forum_id($forum_id)
	{
		$this->set_default_property(self :: PROPERTY_FORUM_ID, $forum_id);
	}
	/**
	 * Returns the author of this ForumPublication.
	 * @return the author.
	 */
	function get_author()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR);
	}

	/**
	 * Sets the author of this ForumPublication.
	 * @param author
	 */
	function set_author($author)
	{
		$this->set_default_property(self :: PROPERTY_AUTHOR, $author);
	}
	/**
	 * Returns the date of this ForumPublication.
	 * @return the date.
	 */
	function get_date()
	{
		return $this->get_default_property(self :: PROPERTY_DATE);
	}

	/**
	 * Sets the date of this ForumPublication.
	 * @param date
	 */
	function set_date($date)
	{
		$this->set_default_property(self :: PROPERTY_DATE, $date);
	}

	function delete()
	{
		$dm = ForumDataManager :: get_instance();
		return $dm->delete_forum_publication($this);
	}

	function create()
	{
		$dm = ForumDataManager :: get_instance();
		$this->set_id($dm->get_next_forum_publication_id());
       	return $dm->create_forum_publication($this);
	}

	function update()
	{
		$dm = ForumDataManager :: get_instance();
		return $dm->update_forum_publication($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>