<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a validation browser table.
 *
 * This class implements some functions to allow validation browser tables to
 * retrieve information about the validation objects to display.
 */
class ValidationBrowserTableDataProvid extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ValidationManagerComponent $browser
     * @param Condition $condition
     */
    function ValidationBrowserTableDataProvid($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the validation objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @param int $order_direction (SORT_ASC or SORT_DESC)
     * @return ResultSet A set of matching validation objects.
     */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
        $order_property = $this->get_order_property($order_property);
        $order_direction = $this->get_order_direction($order_direction);
        
        return $this->get_browser()->retrieve_validations($this->get_condition(), $order_property, $order_direction, $offset, $count);
    }

    /**
     * Gets the number of validation objects in the table
     * @return int
     */
    function get_object_count()
    {
      
        return $this->get_browser()->count_validations($this->get_condition());
    }
}
?>