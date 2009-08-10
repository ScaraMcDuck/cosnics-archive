<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class ItemBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param RepositoryManagerComponent $browser
   * @param Condition $condition
   */
  function ItemBrowserTableDataProvider($browser, $condition)
  {
		parent :: __construct($browser, $condition);
  }
  /**
   * Gets the learning objects
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of matching learning objects.
   */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
		$order_property = $this->get_order_property($order_property);
		$order_direction = $this->get_order_property($order_direction);
       
      return $this->get_browser()->retrieve_items($this->get_condition(), $offset, $count, $order_property, $order_direction);
      //return $this->get_browser()->retrieve_categories_and_items($this->get_browser()->get_category(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of learning objects in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->get_browser()->count_items($this->get_condition());
      //return $this->get_browser()->count_categories_and_items($this->get_browser()->get_category());
    }
}
?>