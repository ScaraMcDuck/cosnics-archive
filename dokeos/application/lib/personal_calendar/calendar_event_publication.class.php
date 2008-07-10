<?php
/**
 * @package application.lib.calendar_eventr
 */
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_user_path(). 'lib/users_data_manager.class.php';

/**
 *	This class represents a CalendarEventPublication. 
 *
 *	CalendarEventPublication objects have a number of default properties:
 *	- id: the numeric ID of the CalendarEventPublication;
 *	- calendar_event: the numeric object ID of the CalendarEventPublication (from the repository);
 *	- publisher: the publisher of the CalendarEventPublication;
 *	- published: the date when the CalendarEventPublication was "posted";
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */
class CalendarEventPublication
{
	const PROPERTY_ID = 'id';
	const PROPERTY_CALENDAR_EVENT = 'calendar_event';
	const PROPERTY_PUBLISHER = 'publisher';
	const PROPERTY_PUBLISHED = 'published';	
	
	private $id;
	private $defaultProperties;
	
	/**
	 * Creates a new calendar_event object.
	 * @param int $id The numeric ID of the CalendarEventPublication object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the CalendarEventPublication
	 *                                 object. Associative array.
	 */
	function CalendarEventPublication($id = 0, $defaultProperties = array ())
	{
		$this->id = $id;
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this CalendarEventPublication object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this CalendarEventPublication.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all CalendarEventPublications.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_CALENDAR_EVENT, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED);
	}
	
	/**
	 * Sets a default property of this CalendarEventPublication by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default calendar_eventr
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
	 * Returns the id of this CalendarEventPublication.
	 * @return int The CalendarEventPublication id.
	 */
	function get_id()
	{
		return $this->id;
	}
	
	/**
	 * Returns the learning object id from this CalendarEventPublication object
	 * @return int The CalendarEvent ID
	 */
	function get_calendar_event()
	{
		return $this->get_default_property(self :: PROPERTY_CALENDAR_EVENT);
	}
	
	 /**
	  * Returns the user of this CalendarEventPublication object
	  * @return int the user
	  */
	function get_publisher()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER);
	}
	
	/**
	 * Returns the published timestamp of this CalendarEventPublication object
	 * @return Timestamp the published date
	 */
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	} 
	  
	/**
	 * Sets the id of this CalendarEventPublication.
	 * @param int $pm_id The CalendarEventPublication id.
	 */
	function set_id($id)
	{
		$this->id = $id;
	}	
	
	/**
	 * Sets the learning object id of this CalendarEventPublication.
	 * @param Int $id the calendar_event ID.
	 */
	function set_calendar_event($id)
	{
		$this->set_default_property(self :: PROPERTY_CALENDAR_EVENT, $id);
	}
	
	/**
	 * Sets the user of this CalendarEventPublication.
	 * @param int $user the User.
	 */
	function set_publisher($publisher)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
	}
	
	/**
	 * Sets the published date of this CalendarEventPublication.
	 * @param int $published the timestamp of the published date.
	 */
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}
	
	function get_publication_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_object($this->get_calendar_event());
	}
	
	function get_publication_publisher()
	{
		$udm = UsersDataManager :: get_instance();
		return $udm->retrieve_user($this->get_publisher());
	}
	
	/**
	 * Instructs the data manager to create the personal message publication, making it
	 * persistent. Also assigns a unique ID to the publication and sets
	 * the publication's creation date to the current time.
	 * @return boolean True if creation succeeded, false otherwise.
	 */
	function create()
	{
		$now = time();
		$this->set_published($now);
		$pmdm = PersonalCalendarDataManager :: get_instance();
		$id = $pmdm->get_next_calendar_event_publication_id();
		$this->set_id($id);
		return $pmdm->create_calendar_event_publication($this);
	}
	
	/**
	 * Create all needed for migration tool to set the published time manually
	 */
	function create_all()
	{
		$pmdm = PersonalCalendarDataManager :: get_instance();
		$id = $pmdm->get_next_calendar_event_publication_id();
		$this->set_id($id);
		return $pmdm->create_calendar_event_publication($this);
	}
	
	/**
	 * Deletes this publication from persistent storage
	 * @see PersonalCalendarDataManager::delete_calendar_event_publication()
	 */
	function delete()
	{
		return PersonalCalendarDataManager :: get_instance()->delete_calendar_event_publication($this);
	}
	
	/**
	 * Updates this publication in persistent storage
	 * @see PersonalCalendarDataManager::update_calendar_event_publication()
	 */
	function update()
	{
		return PersonalCalendarDataManager :: get_instance()->update_calendar_event_publication($this);
	}
}
?>
