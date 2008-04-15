<?php
/**
 * @package application.lib.menu.menu_publication_table
 */
interface MenuItemTableDataProvider
{
    function get_menu_items($offset, $count, $order_property, $order_direction);

    function get_menu_items_count();
}
?>