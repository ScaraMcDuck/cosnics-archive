<?php
/**
 * @package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_admin_path() . '/lib/registration.class.php';

/**
 * TODO: Add comment
 */
class DefaultRegistrationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultRegistrationTableColumnModel()
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
		$columns[] = new ObjectTableColumn(Registration :: PROPERTY_TYPE, true, true);
		$columns[] = new ObjectTableColumn(Registration :: PROPERTY_NAME, true, true);
		$columns[] = new ObjectTableColumn(Registration :: PROPERTY_STATUS, true, true);
		return $columns;
	}
}
?>