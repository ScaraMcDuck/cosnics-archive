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
class CourseBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param WeblcmsManagerComponent $browser
   * @param Condition $condition
   */
  function CourseBrowserTableDataProvider($browser, $condition)
  {
    parent :: __construct($browser, $condition);
  }
  /**
   * Gets the courses
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of matching courses.
   */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
		$order_property = $this->get_order_property($order_property);
		$order_direction = $this->get_order_property($order_direction);
       
      return $this->get_browser()->retrieve_courses(null, $this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of courses in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->get_browser()->count_courses($this->get_condition());
    }
}
?>