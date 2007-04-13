<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
/**
 * A personcal calendar events bundles a learning object (CalendarEvent) and a
 * user in the application.
 */
class PersonalCalendarEvent
{
	/**
	 * A unique id for this personal calendar event
	 */
	private $id;
	/**
	 * The user to which this personal calendar events belongs
	 */
	private $user_id;
	/**
	 * The actual event
	 */
	private $event;
	/**
	 * The date on which the personal calendar event was published
	 */
	private $publication_date;
	/**
	 * Creates a new personal calendar event
	 * @param int $id
	 * @param int $user_id
	 * @param CalendarEvent $event
	 * @param int $publication_date;
	 */
	function PersonalCalendarEvent($id, $user_id, $event, $publication_date)
	{
		$this->id = $id;
		$this->user_id = $user_id;
		$this->event = $event;
		$this->publication_date = $publication_date;
	}
	/**
	 * Creates a new PersonalCalendarEvent and stores it in the persistent
	 * storage system.
	 */
	public function create()
	{
		$dm = PersonalCalendarDataManager :: get_instance();
		$id = $dm->get_next_personal_calendar_event_id();
		$this->set_id($id);
		$this->set_publication_date(time());
		return $dm->create_personal_calendar_event($this);
	}
	/**
	 * Deletes the publication of this event in the personal calendar.
	 */
	public function delete()
	{
		$dm = PersonalCalendarDataManager :: get_instance();
		return $dm->delete_personal_calendar_event($this);
	}
	/**
	 * Loads a personal calendar event
	 * @param int $id
	 * @return PersonalCalendarEvent
	 */
	public function load($id)
	{
		$dm = PersonalCalendarDataManager :: get_instance();
		return $dm->load_personal_calendar_event($id);
	}
	/**
	 * Gets the id of this personal calendar event
	 * @return int
	 */
	public function get_id()
	{
		return $this->id;
	}
	/**
	 * Sets the id of this personal calendar event
	 * @param int $id
	 */
	public function set_id($id)
	{
		$this->id = $id;
	}
	/**
	 * Sets the publication date
	 * @param int
	 */
	public function set_publication_date($publication_date)
	{
		$this->publication_date = $publication_date;
	}
	/**
	 * Gets the event related to this personal calendar event
	 * @return CalendarEvent
	 */
	public function get_event()
	{
		return $this->event;
	}
	/**
	 * Gets the user id of the user to which this personal calendar event
	 * belongs.
	 * @return int
	 */
	public function get_user_id()
	{
		return $this->user_id;
	}
	/**
	 * Gets the publication date
	 * @return int
	 */
	public function get_publication_date()
	{
		return $this->publication_date;
	}
}
?>