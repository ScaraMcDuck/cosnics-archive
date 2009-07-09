<?php
/**
 * $Id: repository_browser_table_column_model.class.php 15472 2008-05-27 18:47:47Z Scara84 $
 * @package repository.repositorymanager
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';

/**
 * Table column model for the repository browser table
 */
class UserViewBrowserTableColumnModel extends ObjectTableColumnModel
{
	function UserViewBrowserTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
		$this->set_default_order_column(1);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(UserView :: PROPERTY_NAME);
		$columns[] = new ObjectTableColumn(UserView :: PROPERTY_DESCRIPTION);
		$columns[] = self :: get_modification_column();
		return $columns;
	}

	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new StaticTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
