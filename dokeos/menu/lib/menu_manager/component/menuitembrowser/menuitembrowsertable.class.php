<?php
/**
 * @package application.lib.menu.menu_manager.component.menupublicationbrowser
 */
require_once dirname(__FILE__).'/../../../menu_item_table/menuitemtable.class.php';
require_once dirname(__FILE__).'/menuitembrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/menuitembrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/menuitembrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../menumanager.class.php';
/**
 * Table to display a set of learning objects.
 */
class MenuItemBrowserTable extends MenuItemTable
{
	/**
	 * Constructor
	 */
	function MenuItemBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new MenuItemBrowserTableColumnModel($browser);
		$renderer = new MenuItemBrowserTableCellRenderer($browser);
		$data_provider = new MenuItemBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$actions = array();
		$actions[MenuManager :: PARAM_DELETE_SELECTED] = Translation :: get('RemoveSelected');
		$user = $browser->get_user();
		$this->set_form_actions($actions);
		$this->set_default_row_count(10);
	}
}
?>