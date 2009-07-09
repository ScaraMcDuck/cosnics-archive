<?php
/**
 * @package application.lib.menu.menu_manager.component.menupublicationbrowser
 */
require_once dirname(__FILE__) . '/../../../navigation_item_table/default_navigation_item_table_column_model.class.php';
/**
 * Table column model for the publication browser table
 */
class NavigationItemBrowserTableColumnModel extends DefaultNavigationItemTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function NavigationItemBrowserTableColumnModel($browser)
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
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>
