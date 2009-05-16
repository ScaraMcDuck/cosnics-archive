<?php
/**
 * $Id$
 * CourseGroup tool
 * @package application.weblcms.tool
 * @subpackage course_group
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
class CourseGroupSubscribedUserBrowserTableDataprovider  extends ObjectTableDataProvider
{
  private $wdm;

  /**
   * Constructor
   * @param WeblcmsManagerComponent $browser
   * @param Condition $condition
   */
  function CourseGroupSubscribedUserBrowserTableDataprovider($browser, $condition)
  {
		parent :: __construct($browser, $condition);
    	$this->wdm = WeblcmsDataManager :: get_instance();
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

      return $this->wdm->retrieve_course_group_users($this->get_browser()->get_course_group(),$this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of users in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->wdm->count_course_group_users($this->get_browser()->get_course_group(),$this->get_condition());
    }
}
?>