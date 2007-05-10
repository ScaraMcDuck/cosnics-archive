<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../../../course/course_table/coursetabledataprovider.class.php';
/**
 * Data provider for a weblcms browser table.
 *
 * This class implements some functions to allow weblcsm browser tables to
 * retrieve information about the course objects to display.
 */
class UnsubscribeBrowserTableDataProvider implements CourseTableDataProvider
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
  function UnsubscribeBrowserTableDataProvider($browser, $condition)
  {
    $this->browser = $browser;
    $this->condition = $condition;
  }
  /**
   * Gets the courses
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of matching courses.
   */
    function get_courses($user = null, $category = null, $offset, $count, $order_property, $order_direction)
    {
      // We always use title as second sorting parameter
      $order_property = array($order_property);
      $order_direction = array($order_direction);
       
      return $this->browser->retrieve_user_courses($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of courses in the table
   * @return int
   */
    function get_course_count()
    {
      return $this->browser->count_user_courses($this->get_condition());
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