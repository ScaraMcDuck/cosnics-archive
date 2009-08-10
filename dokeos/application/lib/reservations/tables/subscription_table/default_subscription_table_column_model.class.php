<?php
/**
 * @package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../item.class.php';

/**
 * TODO: Add comment
 */
class DefaultSubscriptionTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultSubscriptionTableColumnModel($browser)
	{
		parent :: __construct(self :: get_default_columns($browser), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns($browser)
	{
		$columns = array();
		
		if(get_class($browser) == 'ReservationsManagerAdminSubscriptionBrowserComponent')
		{
			$columns[] = new ObjectTableColumn(Subscription :: PROPERTY_USER_ID, true);
		}
		else
		{
			$columns[] = new ObjectTableColumn(Subscription :: PROPERTY_RESERVATION_ID, true);
		}
		$columns[] = new ObjectTableColumn(Subscription :: PROPERTY_START_TIME, true);
		$columns[] = new ObjectTableColumn(Subscription :: PROPERTY_STOP_TIME, true);
		$columns[] = new ObjectTableColumn(Subscription :: PROPERTY_ACCEPTED, true);
		return $columns;
	}
}
?>