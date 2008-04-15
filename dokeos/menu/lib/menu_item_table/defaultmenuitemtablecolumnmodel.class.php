<?php
/**
 * @package application.lib.menu.menu_publication_table
 */
require_once dirname(__FILE__).'/menuitemtablecolumnmodel.class.php';
require_once dirname(__FILE__).'/menuitemtablecolumn.class.php';
require_once dirname(__FILE__).'/../menuitem.class.php';

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