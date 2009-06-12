<?php 
/**
 * wiki
 */

/**
 * This class describes a WikiPublication data object
 *
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiPublication
{
	const CLASS_NAME = __CLASS__;

	/**
	 * WikiPublication properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LEARNING_OBJECT = 'learning_object';
	const PROPERTY_PARENT_ID = 'parent_id';
	const PROPERTY_CATEGORY = 'category';
	const PROPERTY_FROM_DATE = 'from_date';
	const PROPERTY_TO_DATE = 'to_date';
	const PROPERTY_HIDDEN = 'hidden';
	const PROPERTY_PUBLISHER = 'publisher';
	const PROPERTY_PUBLISHED = 'published';
	const PROPERTY_MODIFIED = 'modified';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	const PROPERTY_EMAIL_SENT = 'email_sent';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new WikiPublication object
	 * @param array $defaultProperties The default properties
	 */
	function WikiPublication($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_LEARNING_OBJECT, self :: PROPERTY_PARENT_ID, self :: PROPERTY_CATEGORY, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self :: PROPERTY_MODIFIED, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_EMAIL_SENT);
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
	 * Returns the id of this WikiPublication.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this WikiPublication.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the learning_object of this WikiPublication.
	 * @return the learning_object.
	 */
	function get_learning_object()
	{
		return $this->get_default_property(self :: PROPERTY_LEARNING_OBJECT);
	}

	/**
	 * Sets the learning_object of this WikiPublication.
	 * @param learning_object
	 */
	function set_learning_object($learning_object)
	{
		$this->set_default_property(self :: PROPERTY_LEARNING_OBJECT, $learning_object);
	}
	/**
	 * Returns the parent_id of this WikiPublication.
	 * @return the parent_id.
	 */
	function get_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ID);
	}

	/**
	 * Sets the parent_id of this WikiPublication.
	 * @param parent_id
	 */
	function set_parent_id($parent_id)
	{
		$this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
	}
	/**
	 * Returns the category of this WikiPublication.
	 * @return the category.
	 */
	function get_category()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY);
	}

	/**
	 * Sets the category of this WikiPublication.
	 * @param category
	 */
	function set_category($category)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY, $category);
	}
	/**
	 * Returns the from_date of this WikiPublication.
	 * @return the from_date.
	 */
	function get_from_date()
	{
		return $this->get_default_property(self :: PROPERTY_FROM_DATE);
	}

	/**
	 * Sets the from_date of this WikiPublication.
	 * @param from_date
	 */
	function set_from_date($from_date)
	{
		$this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
	}
	/**
	 * Returns the to_date of this WikiPublication.
	 * @return the to_date.
	 */
	function get_to_date()
	{
		return $this->get_default_property(self :: PROPERTY_TO_DATE);
	}

	/**
	 * Sets the to_date of this WikiPublication.
	 * @param to_date
	 */
	function set_to_date($to_date)
	{
		$this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
	}
	/**
	 * Returns the hidden of this WikiPublication.
	 * @return the hidden.
	 */
	function get_hidden()
	{
		return $this->get_default_property(self :: PROPERTY_HIDDEN);
	}

	/**
	 * Sets the hidden of this WikiPublication.
	 * @param hidden
	 */
	function set_hidden($hidden)
	{
		$this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
	}
	/**
	 * Returns the publisher of this WikiPublication.
	 * @return the publisher.
	 */
	function get_publisher()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER);
	}

	/**
	 * Sets the publisher of this WikiPublication.
	 * @param publisher
	 */
	function set_publisher($publisher)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
	}
	/**
	 * Returns the published of this WikiPublication.
	 * @return the published.
	 */
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	}

	/**
	 * Sets the published of this WikiPublication.
	 * @param published
	 */
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}
	/**
	 * Returns the modified of this WikiPublication.
	 * @return the modified.
	 */
	function get_modified()
	{
		return $this->get_default_property(self :: PROPERTY_MODIFIED);
	}

	/**
	 * Sets the modified of this WikiPublication.
	 * @param modified
	 */
	function set_modified($modified)
	{
		$this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
	}
	/**
	 * Returns the display_order of this WikiPublication.
	 * @return the display_order.
	 */
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}

	/**
	 * Sets the display_order of this WikiPublication.
	 * @param display_order
	 */
	function set_display_order($display_order)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
	}
	/**
	 * Returns the email_sent of this WikiPublication.
	 * @return the email_sent.
	 */
	function get_email_sent()
	{
		return $this->get_default_property(self :: PROPERTY_EMAIL_SENT);
	}

	/**
	 * Sets the email_sent of this WikiPublication.
	 * @param email_sent
	 */
	function set_email_sent($email_sent)
	{
		$this->set_default_property(self :: PROPERTY_EMAIL_SENT, $email_sent);
	}

	function delete()
	{
		$dm = WikiDataManager :: get_instance();
		return $dm->delete_wiki_publication($this);
	}

	function create()
	{
		$dm = WikiDataManager :: get_instance();
		$this->set_id($dm->get_next_wiki_publication_id());
       	return $dm->create_wiki_publication($this);
	}

	function update()
	{
		$dm = WikiDataManager :: get_instance();
		return $dm->update_wiki_publication($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>