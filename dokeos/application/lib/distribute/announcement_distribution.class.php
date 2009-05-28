<?php
/**
 * distribute
 */

/**
 * This class describes a AnnouncementPublication data object
 *
 * @author Hans De Bisschop
 */
class AnnouncementPublication
{
	const CLASS_NAME = __CLASS__;

	/**
	 * AnnouncementPublication properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_ANNOUNCEMENT = 'announcement';
	const PROPERTY_PUBLISHER = 'publisher';
	const PROPERTY_PUBLISHED = 'published';
	const PROPERTY_STATUS = 'status';

	const STATUS_ADDED = 1;
	const STATUS_VERIFIED = 2;
	const STATUS_REFUSED = 3;
	const STATUS_SENT = 4;

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new AnnouncementPublication object
	 * @param array $defaultProperties The default properties
	 */
	function AnnouncementPublication($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_ANNOUNCEMENT, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self :: PROPERTY_STATUS);
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
	 * Returns the id of this AnnouncementPublication.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this AnnouncementPublication.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the announcement of this AnnouncementPublication.
	 * @return the announcement.
	 */
	function get_announcement()
	{
		return $this->get_default_property(self :: PROPERTY_ANNOUNCEMENT);
	}

	/**
	 * Sets the announcement of this AnnouncementPublication.
	 * @param announcement
	 */
	function set_announcement($announcement)
	{
		$this->set_default_property(self :: PROPERTY_ANNOUNCEMENT, $announcement);
	}
	/**
	 * Returns the publisher of this AnnouncementPublication.
	 * @return the publisher.
	 */
	function get_publisher()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER);
	}

	/**
	 * Sets the publisher of this AnnouncementPublication.
	 * @param publisher
	 */
	function set_publisher($publisher)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
	}
	/**
	 * Returns the published of this AnnouncementPublication.
	 * @return the published.
	 */
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	}

	/**
	 * Sets the published of this AnnouncementPublication.
	 * @param published
	 */
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}

	/**
	 * Returns the status of this AnnouncementPublication.
	 * @return the status.
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	}

	/**
	 * Sets the status of this AnnouncementPublication.
	 * @param status
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}

	function delete()
	{
		$dm = DistributeDataManager :: get_instance();
		return $dm->delete_announcement_publication($this);
	}

	function create()
	{
		$dm = DistributeDataManager :: get_instance();
		$this->set_id($dm->get_next_announcement_publication_id());
       	return $dm->create_announcement_publication($this);
	}

	function update()
	{
		$dm = DistributeDataManager :: get_instance();
		return $dm->update_announcement_publication($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>