<?php
/**
 * $Id: personal_calendar_mini_month_renderer.class.php 15496 2008-05-30 08:43:29Z Scara84 $
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/reservations_calendar_renderer.class.php');
require_once (Path :: get_library_path().'html/calendar/mini_month_calendar.class.php');
/**
 * This personal calendar renderer provides a tabular month view to navigate in
 * the calendar
 */
class ReservationsCalendarMiniMonthRenderer extends ReservationsCalendarRenderer
{
	/**
	 * @see ReservationsCalendarRenderer::render()
	 */
	public function render()
	{
		$calendar = new MiniMonthCalendar($this->get_time());
		$from_date = strtotime(date('Y-m-1',$this->get_time()));
		$to_date = strtotime('-1 Second',strtotime('Next Month',$from_date));
		$db_from = DokeosUtilities :: to_db_date($from_date);
		$db_to = DokeosUtilities :: to_db_date($to_date);
		
		$rdm = ReservationsDataManager :: get_instance();
		
		$conditions[] = $rdm->get_reservations_condition($db_from, $db_to, $_GET['item_id']);
		$conditions[] = new EqualityCondition(Reservation :: PROPERTY_STATUS, Reservation :: STATUS_NORMAL);
		$condition = new AndCondition($conditions);
		
		$reservations = $rdm->retrieve_reservations($condition);
		while($reservation = $reservations->next_result())
			$res[] = $reservation;
			
		$html = array();
		
		$start_time = $calendar->get_start_time();
		$end_time = $calendar->get_end_time();
		$table_date = $start_time;
		
		while($table_date <= $end_time)
		{
			$next_table_date = strtotime('+24 Hours',$table_date);
			
			foreach($res as $index => $reservation)
			{ 
				if (!$calendar->contains_events_for_time($table_date))
				{
					$start_date = DokeosUtilities :: time_from_datepicker($reservation->get_start_date());
					$end_date = DokeosUtilities :: time_from_datepicker($reservation->get_stop_date());
					if ($table_date < $start_date && $start_date < $next_table_date || $table_date <= $end_date && $end_date <= $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
					{ 
						
						$content = $this->render_reservation($reservation);
						$calendar->add_event($table_date, $content);
					}
				}
			}
			$table_date = $next_table_date;
		}
		
		$parameters['time'] = '-TIME-';
		$parameters['item_id'] = $_GET['item_id'];
		$calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
		$calendar->mark_period(MiniMonthCalendar::PERIOD_WEEK);
		$calendar->add_navigation_links($this->get_parent()->get_url($parameters));
		$html = $calendar->toHtml();
		return $html;
	}
	/**
	 * Gets a html representation of a published calendar event
	 * @param ReservationsCalendarEvent $event
	 * @return string
	 */
	private function render_reservation($reservation)
	{
		$html[] = '<br /><img src="'.Theme :: get_theme_path().'action_posticon.png"/>';
		return implode("\n",$html);
	}
}
?>