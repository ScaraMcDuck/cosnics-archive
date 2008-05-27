<?php
/**
 * @package users.lib.user_table
 */
require_once dirname(__FILE__).'/user_table_column_model.class.php';
require_once dirname(__FILE__).'/user_table_column.class.php';
require_once dirname(__FILE__).'/../user.class.php';

class DefaultUserTableColumnModel extends UserTableColumnModel
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
		$columns[] = new UserTableColumn(User :: PROPERTY_PICTURE_URI, true);
		$columns[] = new UserTableColumn(User :: PROPERTY_LASTNAME, true);
		$columns[] = new UserTableColumn(User :: PROPERTY_FIRSTNAME, true);
		return $columns;
	}
}
?>