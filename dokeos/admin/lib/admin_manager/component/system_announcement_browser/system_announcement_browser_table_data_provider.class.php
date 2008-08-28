<?php
/**
 * @package application.lib.profiler.profiler_manager.component.profilepublicationbrowser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a profile browser table.
 *
 * This class implements some functions to allow profile browser tables to
 * retrieve information about the profile objects to display.
 */
class SystemAnnouncementBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param ProfileManagerComponent $browser
   * @param Condition $condition
   */
  function SystemAnnouncementBrowserTableDataProvider($browser, $condition)
  {
		parent :: __construct($browser, $condition);
  }
  /**
   * Gets the profile objects
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of matching profile objects.
   */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
		$order_property = $this->get_order_property($order_property);
		$order_direction = $this->get_order_property($order_direction);
    	
      return $this->get_browser()->retrieve_system_announcements($this->get_condition(), $order_property, $order_direction, $offset, $count);
    }
  /**
   * Gets the number of profile objects in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->get_browser()->count_system_announcements($this->get_condition());
    }
}
?>