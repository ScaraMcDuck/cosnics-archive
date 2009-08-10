<?php
/**
 * @package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../reservation.class.php';

/**
 * TODO: Add comment
 */
class DefaultReservationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultReservationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(Reservation :: PROPERTY_TYPE, true);
		$columns[] = new ObjectTableColumn(Reservation :: PROPERTY_START_DATE, true);
		$columns[] = new ObjectTableColumn(Reservation :: PROPERTY_STOP_DATE, true);
		$columns[] = new ObjectTableColumn(Reservation :: PROPERTY_START_SUBSCRIPTION, true);
		$columns[] = new ObjectTableColumn(Reservation :: PROPERTY_STOP_SUBSCRIPTION, true);
		$columns[] = new ObjectTableColumn(Reservation :: PROPERTY_MAX_USERS, true);
		$columns[] = new ObjectTableColumn(Reservation :: PROPERTY_NOTES, true);
		return $columns;
	}
}
?>