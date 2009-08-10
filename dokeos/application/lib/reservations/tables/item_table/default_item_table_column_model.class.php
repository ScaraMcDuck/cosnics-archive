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
class DefaultItemTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultItemTableColumnModel()
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
		$columns[] = new ObjectTableColumn('', false);
		$columns[] = new ObjectTableColumn(Item :: PROPERTY_NAME, true);
		$columns[] = new ObjectTableColumn(Item :: PROPERTY_DESCRIPTION, true);
		$columns[] = new ObjectTableColumn(Item :: PROPERTY_RESPONSIBLE, true);
		$columns[] = new ObjectTableColumn(Item :: PROPERTY_CREDITS, true);
		return $columns;
	}
}
?>