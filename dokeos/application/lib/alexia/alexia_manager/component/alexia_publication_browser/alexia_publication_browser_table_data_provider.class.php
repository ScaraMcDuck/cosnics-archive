<?php
/**
 * @package application.lib.alexiar.alexiar_manager.component.alexiapublicationbrowser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a alexia browser table.
 *
 * This class implements some functions to allow alexia browser tables to
 * retrieve information about the alexia objects to display.
 */
class AlexiaPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param AlexiaManagerComponent $browser
     * @param Condition $condition
     */
    function AlexiaPublicationBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the alexia objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @param int $order_direction (SORT_ASC or SORT_DESC)
     * @return ResultSet A set of matching alexia objects.
     */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
        $order_property = $this->get_order_property($order_property);
        $order_direction = $this->get_order_direction($order_direction);
        
        return $this->get_browser()->retrieve_alexia_publications($this->get_condition(), $order_property, $order_direction, $offset, $count);
    }

    /**
     * Gets the number of alexia objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_alexia_publications($this->get_condition());
    }
}
?>