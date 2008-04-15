<?php
/**
 * @package application.lib.menu.menu_manager.component.menupublicationbrowser
 */
require_once dirname(__FILE__).'/../../../menu_item_table/menuitemtabledataprovider.class.php';
/**
 * Data provider for a menu browser table.
 *
 * This class implements some functions to allow menu browser tables to
 * retrieve information about the menu objects to display.
 */
class MenuItemBrowserTableDataProvider implements MenuItemTableDataProvider
{
  /**
   * The menu manager component in which the table will be displayed
   */
  private $browser;
  /**
   * The condition used to select the menu objects
   */
  private $condition;
  /**
   * Constructor
   * @param MenuManagerManagerComponent $browser
   * @param Condition $condition
   */
  function MenuItemBrowserTableDataProvider($browser, $condition)
  {
    $this->browser = $browser;
    $this->condition = $condition;
  }
  /**
   * Gets the menu objects
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of matching menu objects.
   */
    function get_menu_items($offset, $count, $order_property, $order_direction)
    {
      $order_property = array('sort' ,$order_property);
      $order_direction = array(SORT_ASC, $order_direction);
      
      return $this->browser->retrieve_menu_items($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of menu objects in the table
   * @return int
   */
    function get_menu_items_count()
    {
      return $this->browser->count_menu_items($this->get_condition());
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
	 * @return MenuManagerManagerComponent
	 */
    protected function get_browser()
    {
      return $this->browser;
    }
}
?>