<?php
abstract class PersonalCalendarRenderer
{
	private $personal_calendar;
	function PersonalCalendarRenderer($personal_calendar)
	{
		$this->personal_calendar = $personal_calendar;
	}
	public function get_events($from_date,$to_date)
	{
		return $this->personal_calendar->get_events($from_date,$to_date);
	}
	abstract function render();
}
?>