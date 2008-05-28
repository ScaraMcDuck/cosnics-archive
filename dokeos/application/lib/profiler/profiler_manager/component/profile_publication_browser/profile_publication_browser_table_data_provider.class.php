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
class ProfilePublicationBrowserTableDataProvider implements ObjectTableDataProvider
{
  /**
   * The profile manager component in which the table will be displayed
   */
  private $browser;
  /**
   * The condition used to select the profile objects
   */
  private $condition;
  /**
   * Constructor
   * @param ProfileManagerComponent $browser
   * @param Condition $condition
   */
  function ProfilePublicationBrowserTableDataProvider($browser, $condition)
  {
    $this->browser = $browser;
    $this->condition = $condition;
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
    	
      return $this->browser->retrieve_profile_publications($this->get_condition(), $order_property, $order_direction, $offset, $count);
    }
  /**
   * Gets the number of profile objects in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->browser->count_profile_publications($this->get_condition());
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
	 * @return ProfileManagerComponent
	 */
    protected function get_browser()
    {
      return $this->browser;
    }
}
?>