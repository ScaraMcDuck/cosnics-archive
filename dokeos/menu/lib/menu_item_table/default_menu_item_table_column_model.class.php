<?php
/**
 * @package application.lib.menu.menu_publication_table
 */
require_once dirname(__FILE__).'/menu_item_table_column_model.class.php';
require_once dirname(__FILE__).'/menu_item_table_column.class.php';
require_once dirname(__FILE__).'/../menu_item.class.php';

class DefaultMenuItemTableColumnModel extends MenuItemTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultMenuItemTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return MenuManagerTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new MenuItemTableColumn(MenuItem :: PROPERTY_TITLE, true, false);
		return $columns;
	}
}
?>