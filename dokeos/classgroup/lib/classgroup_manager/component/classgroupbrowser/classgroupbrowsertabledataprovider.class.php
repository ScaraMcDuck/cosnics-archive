<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../classgroup_table/classgrouptabledataprovider.class.php';
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class ClassGroupBrowserTableDataProvider implements ClassGroupTableDataProvider
{
  /**
   * The repository manager component in which the table will be displayed
   */
  private $browser;
  /**
   * The condition used to select the learning objects
   */
  private $condition;
  /**
   * Constructor
   * @param RepositoryManagerComponent $browser
   * @param Condition $condition
   */
  function ClassGroupBrowserTableDataProvider($browser, $condition)
  {
    $this->browser = $browser;
    $this->condition = $condition;
  }
  /**
   * Gets the learning objects
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of matching learning objects.
   */
    function get_classgroups($group = null, $category = null, $offset, $count, $order_property, $order_direction)
    {
      // We always use title as second sorting parameter
      $order_property = array($order_property);
      $order_direction = array($order_direction);
       
      return $this->browser->retrieve_classgroups($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of learning objects in the table
   * @return int
   */
    function get_classgroup_count()
    {
      return $this->browser->count_classgroups($this->get_condition());
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