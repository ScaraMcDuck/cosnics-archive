<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../tables/reservation_table/default_reservation_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../reservation.class.php';
/**
 * Table column model for the user browser table
 */
class ReservationBrowserTableColumnModel extends DefaultReservationTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function ReservationBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(1);
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new StaticTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
