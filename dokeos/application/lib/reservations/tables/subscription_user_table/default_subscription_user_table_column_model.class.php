<?php
/**
 * @package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';

/**
 * TODO: Add comment
 */
class DefaultSubscriptionUserTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultSubscriptionUserTableColumnModel($browser)
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
		$columns[] = new ObjectTableColumn(SubscriptionUser :: PROPERTY_USER_ID, true);
		return $columns;
	}
}
?>