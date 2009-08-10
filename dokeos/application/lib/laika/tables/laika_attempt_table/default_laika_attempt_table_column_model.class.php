<?php
/**
 * @package repository.publicationtable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../laika_attempt.class.php';

/**
 * TODO: Add comment
 */
class DefaultLaikaAttemptTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultLaikaAttemptTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 3);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(LaikaAttempt :: PROPERTY_DATE);
		return $columns;
	}
}
?>