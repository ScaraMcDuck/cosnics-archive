<?php
/**
 * @package application.lib.menu.menu_manager.component.menupublicationbrowser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
require_once dirname(__FILE__) . '/../../../navigation_item.class.php';
/**
 * Data provider for a menu browser table.
 *
 * This class implements some functions to allow menu browser tables to
 * retrieve information about the menu objects to display.
 */
class NavigationItemBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param MenuManagerManagerComponent $browser
     * @param Condition $condition
     */
    function NavigationItemBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the menu objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @param int $order_direction (SORT_ASC or SORT_DESC)
     * @return ResultSet A set of matching menu objects.
     */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
        $order_property = $this->get_order_property($order_property);
        $order_direction = $this->get_order_direction($order_direction);

        // We always use title as second sorting parameter
//        $order_property = array(NavigationItem :: PROPERTY_SORT);
//        $order_direction = array(SORT_ASC);

        $order_property[] = new ObjectTableOrder(NavigationItem :: PROPERTY_SORT);
        return $this->get_browser()->retrieve_navigation_items($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }

    /**
     * Gets the number of menu objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_navigation_items($this->get_condition());
    }
}
?>