<?php
/**
 * @package users.lib.user_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../user.class.php';

class DefaultUserTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultUserTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return UserTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(User :: PROPERTY_PICTURE_URI, true);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true);
		return $columns;
	}
}
?>