<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../browser/repository_browser_table_data_provider.class.php';
/**
 * Data provider for the recycle bin browser table
 */
class RecycleBinBrowserTableDataProvider extends RepositoryBrowserTableDataProvider
{
	/**
	 * Constructor
	 * @param RepositoryManagerRecycleBinBrowserComponent $browser
	 * @param Condition $condition
	 */
	function RecycleBinBrowserTableDataProvider($browser, $condition)
	{
		parent :: __construct($browser, $condition);
	}
	// Inherited
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
    	
      	// We always use title as second sorting parameter
		$order_property[] = LearningObject :: PROPERTY_TITLE;
		$order_direction[] = SORT_ASC;
		
    	$objects = $this->get_browser()->retrieve_learning_objects(null, $this->get_condition(), $order_property, $order_direction, $offset, $count, LearningObject :: STATE_RECYCLED, false);
    
    	return $objects;
    }
	// Inherited
    function get_object_count()
    {
    	return $this->get_browser()->count_learning_objects(null, $this->get_condition(), LearningObject :: STATE_RECYCLED, false);
    }
}
?>