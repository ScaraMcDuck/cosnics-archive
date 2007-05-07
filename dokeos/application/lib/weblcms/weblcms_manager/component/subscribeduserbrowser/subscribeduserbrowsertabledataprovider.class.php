<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../../../../users/lib/user_table/usertabledataprovider.class.php';
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class SubscribedUserBrowserTableDataProvider implements UserTableDataProvider
{
  /**
   * The repository manager component in which the table will be displayed
   */
  private $browser;
  /**
   * The condition used to select the learning objects
   */
  private $condition;
  
  private $udm;
  
  /**
   * Constructor
   * @param RepositoryManagerComponent $browser
   * @param Condition $condition
   */
  function SubscribedUserBrowserTableDataProvider($browser, $condition)
  {
    $this->browser = $browser;
    $this->condition = $condition;
    $this->udm = UsersDataManager :: get_instance($browser->get_user_id());
  }
  /**
   * Gets the learning objects
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of matching learning objects.
   */
    function get_users($user = null, $category = null, $offset, $count, $order_property, $order_direction)
    {
      // We always use title as second sorting parameter
      $order_property = array($order_property);
      $order_direction = array($order_direction);
       
      return $this->udm->retrieve_users($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of learning objects in the table
   * @return int
   */
    function get_user_count()
    {
      return $this->udm->count_users($this->get_condition());
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
	 * @return RepositoryManagerComponent
	 */
    protected function get_browser()
    {
      return $this->browser;
    }
}
?>