<?php
/**
 * @package users.lib.usermanager.component.adminuserbrowser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a user browser table.
 *
 * This class implements some functions to allow user browser tables to
 * retrieve information about the users to display.
 */
class AdminUserBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param UserManagerComponent $browser
   * @param Condition $condition
   */
  function AdminUserBrowserTableDataProvider($browser, $condition)
  {
		parent :: __construct($browser, $condition);
  }
  /**
   * Gets the users
   * @param String $user
   * @param String $category
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
       
      return $this->get_browser()->retrieve_users($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of users in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->get_browser()->count_users($this->get_condition());
    }
}
?>