<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class PublicationBrowserTableDataProvider implements ObjectTableDataProvider
{
  /**
   * The repository manager component in which the table will be displayed
   */
  private $browser;
  /**
   * The condition used to select the learning objects
   */
  private $condition;
  /**
   * Constructor
   * @param RepositoryManagerComponent $browser
   * @param Condition $condition
   */
  function PublicationBrowserTableDataProvider($browser, $condition)
  {
    $this->browser = $browser;
    $this->condition = $condition;
  }
  /**
   * Gets the learning objects
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

      $publication_attributes = $this->browser->get_learning_object_publication_attributes($this->browser->get_user(), null, 'user', $offset, $count, $order_property, $order_direction);
      return $publication_attributes = array_splice($publication_attributes, $offset, $count);
    }
  /**
   * Gets the number of learning objects in the table
   * @return int
   */
    function get_object_count()
    {
		return $this->browser->count_publication_attributes($this->browser->get_user(), null, $this->get_condition());
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
	 * @return RepositoryManagerComponent
	 */
    protected function get_browser()
    {
      return $this->browser;
    }
}
?>