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
class AdminUserBrowserTableDataProvider implements ObjectTableDataProvider
{
  /**
   * The user manager component in which the table will be displayed
   */
  private $browser;
  /**
   * The condition used to select the users
   */
  private $condition;
  /**
   * Constructor
   * @param UserManagerComponent $browser
   * @param Condition $condition
   */
  function AdminUserBrowserTableDataProvider($browser, $condition)
  {
    $this->browser = $browser;
    $this->condition = $condition;
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
    	if (is_null($order_property))
    	{
    		$order_property = array();
    	}
    	elseif(!is_array($order_property))
    	{
    		$order_property = array($order_property);
    	}
    	
    	if (is_null($order_direction))
    	{
    		$order_direction = array();
    	}
    	elseif(!is_array($order_direction))
    	{
    		$order_direction = array($order_direction);
    	}	
       
      return $this->browser->retrieve_users($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of users in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->browser->count_users($this->get_condition());
    }
  /**
   * Gets the condition
   * @return Condition
   */
    protected function get_condition()
    {
      return $this->condition;
    }
	/**
	 * Gets the browser
	 * @return UserManagerComponent
	 */
    protected function get_browser()
    {
      return $this->browser;
    }
}
?>