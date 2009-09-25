<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../content_object_table/default_content_object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
/**
 * Table column model for the recycle bin browser table
 */
class RecycleBinBrowserTableColumnModel extends DefaultContentObjectTableColumnModel
{
	/**
	 * Column for the action links
	 */
	private static $action_column;
	/**
	 * Constructor
	 */
	function RecycleBinBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(0);
		$col = new ObjectTableColumn(ContentObject :: PROPERTY_PARENT_ID);
		$col->set_title(Translation :: get('OriginalLocation'));
		$this->add_column($col);
		$this->add_column(self :: get_action_column());
	}
	/**
	 * Gets the action column
	 * @return ContentObjectTableColumn
	 */
	static function get_action_column()
	{
		if (!isset(self :: $action_column))
		{
			self :: $action_column = new StaticTableColumn('');
		}
		return self :: $action_column;
	}
}
?>
