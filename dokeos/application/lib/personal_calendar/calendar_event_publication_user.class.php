<?php 
/**
 * weblcms
 */

/**
 * This class describes a LearningObjectPublicationUser data object
 *
 * @author Hans De Bisschop
 */
class CalendarEventPublicationUser
{
	const CLASS_NAME = __CLASS__;
	const TABLE_NAME = 'publication_user';

	/**
	 * LearningObjectPublicationUser properties
	 */
	const PROPERTY_PUBLICATION = 'publication';
	const PROPERTY_USER = 'user';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new LearningObjectPublicationUser object
	 * @param array $defaultProperties The default properties
	 */
	function CalendarEventPublicationUser($defaultProperties = array ())
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
		return array (self :: PROPERTY_PUBLICATION, self :: PROPERTY_USER);
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
	 * Returns the publication of this LearningObjectPublicationUser.
	 * @return the publication.
	 */
	function get_publication()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLICATION);
	}

	/**
	 * Sets the publication of this LearningObjectPublicationUser.
	 * @param publication
	 */
	function set_publication($publication)
	{
		$this->set_default_property(self :: PROPERTY_PUBLICATION, $publication);
	}
	/**
	 * Returns the user of this LearningObjectPublicationUser.
	 * @return the user.
	 */
	function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER);
	}

	/**
	 * Sets the user of this LearningObjectPublicationUser.
	 * @param user
	 */
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}

	function delete()
	{
		$dm = PersonalCalendarDataManager :: get_instance();
		return $dm->delete_calendar_event_publication_user($this);
	}

	function create()
	{
		$dm = PersonalCalendarDataManager :: get_instance();
       	return $dm->create_calendar_event_publication_user($this);
	}

	function update()
	{
		$dm = PersonalCalendarDataManager :: get_instance();
		return $dm->update_calendar_event_publication_user($this);
	}

	static function get_table_name()
	{
		return self :: TABLE_NAME;
	}
}

?>