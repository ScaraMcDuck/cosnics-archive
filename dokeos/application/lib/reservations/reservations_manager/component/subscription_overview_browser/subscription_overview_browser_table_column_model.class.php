<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../tables/subscription_table/default_subscription_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../subscription.class.php';
/**
 * Table column model for the user browser table
 */
class SubscriptionOverviewBrowserTableColumnModel extends DefaultSubscriptionTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function SubscriptionOverviewBrowserTableColumnModel($browser)
	{
		parent :: ObjectTableColumnModel(self :: get_default_columns($browser), 2);
	}
	
	private static function get_default_columns($browser)
	{
		$columns = array();
		$columns[] = new StaticTableColumn(Translation :: get(DokeosUtilities :: underscores_to_camelcase(Subscription :: PROPERTY_RESERVATION_ID)));
		$columns[] = new StaticTableColumn(Translation :: get(DokeosUtilities :: underscores_to_camelcase(Subscription :: PROPERTY_USER_ID)));
		$columns[] = new ObjectTableColumn(Subscription :: PROPERTY_START_TIME, true);
		$columns[] = new ObjectTableColumn(Subscription :: PROPERTY_STOP_TIME, true);
		$columns[] = new ObjectTableColumn(Subscription :: PROPERTY_ACCEPTED, true);
		return $columns;
	}
	
}
?>
