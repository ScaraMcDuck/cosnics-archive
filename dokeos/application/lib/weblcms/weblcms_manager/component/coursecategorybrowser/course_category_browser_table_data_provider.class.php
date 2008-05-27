<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../../../course/course_category_table/course_category_table_data_provider.class.php';
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class CourseCategoryBrowserTableDataProvider implements CourseCategoryTableDataProvider
{
  /**
   * The weblcms component in which the table will be displayed
   */
  private $browser;
  /**
   * The condition used to select the learning objects
   */
  private $condition;
  /**
   * Constructor
   * @param WeblcmsComponent $browser
   * @param Condition $condition
   */
  function CourseCategoryBrowserTableDataProvider($browser, $condition)
  {
    $this->browser = $browser;
    $this->condition = $condition;
  }
  /**
   * Gets the course categories
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of matching course categories.
   */
    function get_course_categories($offset, $count, $order_property, $order_direction)
    {
      // We always use title as second sorting parameter
      $order_property = array($order_property);
      $order_direction = array($order_direction);
       
      return $this->browser->retrieve_course_categories($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of course categories in the table
   * @return int
   */
    function get_course_category_count()
    {
      return $this->browser->count_course_categories($this->get_condition());
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
	 * @return WeblcmsComponent
	 */
    protected function get_browser()
    {
      return $this->browser;
    }
}
?>