<?php
class PersonalCalendarEvent
{
	private $id;
	private $user_id;
	private $event;
	/**
	 * Creates a new personal calendar event
	 * @param int $id
	 * @param int $user_id
	 * @param CalendarEvent $event
	 */
	function PersonalCalendarEvent($id, $user_id, $event)
	{
		$this->id = $id;
		$this->user_id = $user_id;
		$this->event = $event;
	}
	public function create()
	{
		$dm = PersonalCalendarDataManager :: get_instance();
		$id = $dm->get_next_personal_calendar_event_id();
		$this->set_id($id);
		return $dm->create_personal_calendar_event($this);
	}
	public function load($id)
	{
		$dm = PersonalCalendarDataManager :: get_instance();
		return $dm->load_personal_calendar_event($id);
	}
	public function get_id()
	{
		return $this->id;
	}
	public function set_id($id)
	{
		$this->id = $id;
	}
	public function get_event()
	{
		return $this->event;
	}
	public function get_user_id()
	{
		return $this->user_id;
	}
}
?>