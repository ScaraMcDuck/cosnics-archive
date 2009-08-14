<?php
/**
 * @package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../role.class.php';

/**
 * TODO: Add comment
 */
class DefaultRoleTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultRoleTableColumnModel()
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
		$columns[] = new ObjectTableColumn(Role :: PROPERTY_NAME);
		$columns[] = new ObjectTableColumn(Role :: PROPERTY_DESCRIPTION);
		return $columns;
	}
}
?>