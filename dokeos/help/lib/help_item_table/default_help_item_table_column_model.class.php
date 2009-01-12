<?php
/**
 * @package repository.usertable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../help_item.class.php';

/**
 * TODO: Add comment
 */
class DefaultHelpItemTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultHelpItemTableColumnModel()
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
		$columns[] = new ObjectTableColumn(HelpItem :: PROPERTY_NAME, true);
		$columns[] = new ObjectTableColumn(HelpItem :: PROPERTY_URL, true);
		return $columns;
	}
}
?>