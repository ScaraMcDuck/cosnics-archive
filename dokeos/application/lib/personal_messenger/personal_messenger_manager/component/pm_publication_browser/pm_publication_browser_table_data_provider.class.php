<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component.pmpublicationbrowser
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a personal messenger browser table.
 *
 * This class implements some functions to allow personal messenger browser tables to
 * retrieve information about the personal messages to display.
 */
class PmPublicationBrowserTableDataProvider implements ObjectTableDataProvider
{
  /**
   * The personal messenger manager component in which the table will be displayed
   */
  private $browser;
  /**
   * The condition used to select the personal messages
   */
  private $condition;
  /**
   * Constructor
   * @param PersonalMessengerManagerComponent $browser
   * @param Condition $condition
   */
  function PmPublicationBrowserTableDataProvider($browser, $condition)
  {
    $this->browser = $browser;
    $this->condition = $condition;
  }
  /**
   * Gets the personal message publications
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of matching personal message publications.
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
    	
      return $this->browser->retrieve_personal_message_publications($this->get_condition(), $order_property, $order_direction, $offset, $count);
    }
  /**
   * Gets the number of personal message publications in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->browser->count_personal_message_publications($this->get_condition());
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
	 * @return PersonalMessengerManagerComponent
	 */
    protected function get_browser()
    {
      return $this->browser;
    }
}
?>