<?php
/**
 * @package application.lib.profiler.publisher.publication_candidate_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_repository_path(). 'lib/content_object.class.php';
/**
 * This class represents a column model for a publication candidate table
 */
class ContentObjectTableColumnModel extends ObjectTableColumnModel {
	/**
	 * The column with the action buttons.
	 */
	private static $action_column;
	/**
	 * Constructor.
	 */
	function ContentObjectTableColumnModel()
	{
		parent :: __construct(self :: get_columns(), 1, SORT_ASC);
	}
	/**
	 * Gets the columns of this table.
	 * @return array An array of all columns in this table.
	 * @see ContentObjectTableColumn
	 */
	function get_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
		$columns[] = self :: get_action_column();
		return $columns;
	}
	/**
	 * Gets the column wich contains the action buttons.
	 * @return ContentObjectTableColumn The action column.
	 */
	static function get_action_column()
	{
		if (!isset(self :: $action_column))
		{
			self :: $action_column = new StaticTableColumn(Translation :: get('Publish'));
		}
		return self :: $action_column;
	}
}
?>