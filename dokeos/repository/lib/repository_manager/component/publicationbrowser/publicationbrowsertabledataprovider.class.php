<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../publication_table/publicationtabledataprovider.class.php';
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class PublicationBrowserTableDataProvider implements PublicationTableDataProvider
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
    function get_learning_object_publication_attributes($offset, $count, $order_property, $order_direction)
    {
      // We always use title as second sorting parameter
      $order_property = array($order_property);
      $order_direction = array($order_direction);
      
      return $this->browser->get_learning_object_publication_attributes(null, 'user', $offset, $count, $order_property, $order_direction);
//      $publication_attributes = $this->browser->get_learning_object_publication_attributes(null, 'user');
//      $publication_attributes = $this->organize_publication_attributes($publication_attributes, $offset, $count, $order_property, $order_direction);
//      return $publication_attributes;
    }
  /**
   * Gets the number of learning objects in the table
   * @return int
   */
    function get_learning_object_publication_count()
    {
      return $this->browser->count_publication_attributes(null, $this->get_condition());
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
    
    function organize_publication_attributes($publication_attributes, $offset, $count, $order_property, $order_direction)
    {
    	$order = 'by_' . $order_property[0] . '_' . ($order_direction[0] == 3 ? 'asc' : 'desc');
    	
    	usort($publication_attributes, array (get_class(), $order));
    	
    	$publication_attributes = array_splice($publication_attributes, $offset, $count);
    	
    	return $publication_attributes;
    }
    
	private static function by_title_desc($object_1, $object_2)
	{
		return strcasecmp($object_1->get_publication_object()->get_title(), $object_2->get_publication_object()->get_title());
	}
	
	private static function by_title_asc($object_1, $object_2)
	{
		return -strcasecmp($object_1->get_publication_object()->get_title(), $object_2->get_publication_object()->get_title());
	}
	
	private static function by_location_desc($object_1, $object_2)
	{
		return strcasecmp($object_1->get_location(), $object_2->get_location());
	}
	
	private static function by_location_asc($object_1, $object_2)
	{
		return -strcasecmp($object_1->get_location(), $object_2->get_location());
	}
	
	private static function by_application_desc($object_1, $object_2)
	{
		return strcasecmp($object_1->get_application(), $object_2->get_application());
		
	}
	
	private static function by_application_asc($object_1, $object_2)
	{
		return -strcasecmp($object_1->get_application(), $object_2->get_application());
	}
}
?>