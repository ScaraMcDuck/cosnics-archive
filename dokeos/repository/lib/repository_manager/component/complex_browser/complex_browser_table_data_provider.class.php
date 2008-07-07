<?php
/**
 * $Id: repository_browser_table_data_provider.class.php 15484 2008-05-28 13:12:14Z Scara84 $
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class ComplexBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param RepositoryManagerComponent $browser
   * @param Condition $condition
   */
  function ComplexBrowserTableDataProvider($browser, $condition)
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
    	
      	// We always use title as second sorting parameter
		$order_property[] = ComplexLearningObjectItem :: PROPERTY_ID;
		$order_direction[] = SORT_ASC;
		
      return $this->get_browser()->retrieve_complex_learning_object_items($this->get_condition(), $order_property, $order_direction, $offset, $count);
    }
  /**
   * Gets the number of learning objects in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->get_browser()->count_complex_learning_object_items($this->get_condition());
    }
}
?>