<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../reservation.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
/**
 * TODO: Add comment
 */
class DefaultReservationTableCellRenderer implements ObjectTableCellRenderer
{

	/**
	 * Constructor
	 */
	function DefaultReservationTableCellRenderer($browser)
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $reservation)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case Reservation :: PROPERTY_TYPE :
					switch($reservation->get_type())
					{
						case Reservation :: TYPE_TIMEPICKER: return Translation :: get('Timepicker');
						case Reservation :: TYPE_BLOCK: return Translation :: get('Block');
					}
				case Reservation :: PROPERTY_START_DATE :
					return $reservation->get_start_date();
				case Reservation :: PROPERTY_STOP_DATE : 
					return $reservation->get_stop_date();
				case Reservation :: PROPERTY_NOTES :
					$notes = strip_tags($reservation->get_notes());
					if(strlen($notes) > 175)
					{
						$notes = mb_substr($notes,0,170).'&hellip;';
					}
					return  '<div style="word-wrap: break-word; max-width: 250px;" >' . $notes . '</div>';
				case Reservation :: PROPERTY_START_SUBSCRIPTION :
					return $reservation->get_start_subscription();
				case Reservation :: PROPERTY_STOP_SUBSCRIPTION :
					return $reservation->get_stop_subscription();
				case Reservation :: PROPERTY_MAX_USERS :
					return $reservation->get_max_users();
			}

		}
			
		return '&nbsp;';
	}
}
?>