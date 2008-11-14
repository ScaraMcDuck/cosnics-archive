<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
require_once Path :: get_group_path() . 'lib/group_data_manager.class.php';
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class SubscribeGroupBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param RepositoryManagerComponent $browser
   * @param Condition $condition
   */
  function SubscribeGroupBrowserTableDataProvider($browser, $condition)
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
       
      return GroupDataManager :: get_instance()->retrieve_groups($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of learning objects in the table
   * @return int
   */
    function get_object_count()
    {
      return GroupDataManager :: get_instance()->count_groups($this->get_condition());
    }
}
?>