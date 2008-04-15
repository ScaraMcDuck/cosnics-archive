<?php
/**
 * @package application.lib.menu.menu_manager.component.menupublicationbrowser
 */
require_once dirname(__FILE__).'/../../../menu_item_table/defaultmenuitemtablecolumnmodel.class.php';
/**
 * Table column model for the publication browser table
 */
class MenuItemBrowserTableColumnModel extends DefaultMenuItemTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function MenuItemBrowserTableColumnModel($browser)
	{
		parent :: __construct();
		$this->set_default_order_column(0);
		$this->set_default_order_direction(SORT_ASC);
		$user = $browser->get_user();
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return MenuManagerTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new MenuItemTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
