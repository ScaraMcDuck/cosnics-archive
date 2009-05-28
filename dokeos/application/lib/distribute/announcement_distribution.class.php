<?php
/**
 * distribute
 */

/**
 * This class describes a AnnouncementDistribution data object
 *
 * @author Hans De Bisschop
 */
class AnnouncementDistribution
{
	const CLASS_NAME = __CLASS__;

	/**
	 * AnnouncementDistribution properties
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

	private $target_groups;
	private $target_users;

	/**
	 * Creates a new AnnouncementDistribution object
	 * @param array $defaultProperties The default properties
	 */
	function AnnouncementDistribution($defaultProperties = array ())
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
	 * Returns the id of this AnnouncementDistribution.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this AnnouncementDistribution.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the announcement of this AnnouncementDistribution.
	 * @return the announcement.
	 */
	function get_announcement()
	{
		return $this->get_default_property(self :: PROPERTY_ANNOUNCEMENT);
	}

	/**
	 * Sets the announcement of this AnnouncementDistribution.
	 * @param announcement
	 */
	function set_announcement($announcement)
	{
		$this->set_default_property(self :: PROPERTY_ANNOUNCEMENT, $announcement);
	}
	/**
	 * Returns the publisher of this AnnouncementDistribution.
	 * @return the publisher.
	 */
	function get_publisher()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER);
	}

	/**
	 * Sets the publisher of this AnnouncementDistribution.
	 * @param publisher
	 */
	function set_publisher($publisher)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
	}
	/**
	 * Returns the published of this AnnouncementDistribution.
	 * @return the published.
	 */
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	}

	/**
	 * Sets the published of this AnnouncementDistribution.
	 * @param published
	 */
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}

	/**
	 * Returns the status of this AnnouncementDistribution.
	 * @return the status.
	 */
	function get_status()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	}

	/**
	 * Sets the status of this AnnouncementDistribution.
	 * @param status
	 */
	function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}

	function delete()
	{
		$dm = DistributeDataManager :: get_instance();
		return $dm->delete_announcement_distribution($this);
	}

	function create()
	{
		$dm = DistributeDataManager :: get_instance();
		$this->set_id($dm->get_next_announcement_distribution_id());
       	return $dm->create_announcement_distribution($this);
	}

	function update()
	{
		$dm = DistributeDataManager :: get_instance();
		return $dm->update_announcement_distribution($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

	function get_target_users()
	{
		if (!isset($this->target_users))
		{
			$ddm = DistributeDataManager :: get_instance();
			$this->target_users = $ddm->retrieve_announcement_distribution_target_users($this);
		}

		return $this->target_users;
	}

	function get_target_groups()
	{
		if (!isset($this->target_groups))
		{
			$ddm = DistributeDataManager :: get_instance();
			$this->target_groups = $ddm->retrieve_announcement_distribution_target_groups($this);
		}

		return $this->target_groups;
	}

	function set_target_users($target_users)
	{
		$this->target_users = $target_users;
	}

	function set_target_groups($target_groups)
	{
		$this->target_groups = $target_groups;
	}
}
?>