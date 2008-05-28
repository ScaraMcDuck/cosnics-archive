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
class AdminCourseBrowserTableDataProvider implements ObjectTableDataProvider
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
  function AdminCourseBrowserTableDataProvider($browser, $condition)
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
       
      return $this->browser->retrieve_courses(null, null, $this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of courses in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->browser->count_courses($this->get_condition());
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