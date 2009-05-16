<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class SubscribedUserBrowserTableDataProvider extends ObjectTableDataProvider
{  
  private $udm;
  
  /**
   * Constructor
   * @param WeblcmsManagerComponent $browser
   * @param Condition $condition
   */
  function SubscribedUserBrowserTableDataProvider($browser, $condition)
  {
    parent :: __construct($browser, $condition);
    $this->udm = UserDataManager :: get_instance($browser->get_user_id());
  }
  /**
   * Gets the users
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
       
      return $this->udm->retrieve_users($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of users in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->udm->count_users($this->get_condition());
    }
}
?>