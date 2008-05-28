<?php
/**
 * $Id$
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
class GroupUnsubscribedUserBrowserTableDataprovider implements ObjectTableDataProvider
{
  /**
   * The weblcms component in which the table will be displayed
   */
  private $browser;
  /**
   * The condition used to select the users
   */
  private $condition;

  private $wdm;

  /**
   * Constructor
   * @param WeblcmsComponent $browser
   * @param Condition $condition
   */
  function GroupUnsubscribedUserBrowserTableDataprovider($browser, $condition)
  {
    $this->browser = $browser;
    $this->condition = $condition;
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

      return $this->wdm->retrieve_possible_group_users($this->browser->get_group(),$this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of users in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->wdm->count_possible_group_users($this->browser->get_group(),$this->get_condition());
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
	 * @return WeblcsmComponent
	 */
    protected function get_browser()
    {
      return $this->browser;
    }
}
?>