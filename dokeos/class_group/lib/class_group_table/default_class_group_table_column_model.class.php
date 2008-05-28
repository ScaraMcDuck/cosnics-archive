<?php
/**
 * @package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../class_group.class.php';

/**
 * TODO: Add comment
 */
class DefaultClassGroupTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultClassGroupTableColumnModel()
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
		$columns[] = new ObjectTableColumn(ClassGroup :: PROPERTY_NAME, true);
		$columns[] = new ObjectTableColumn(ClassGroup :: PROPERTY_DESCRIPTION, true);
		return $columns;
	}
}
?>