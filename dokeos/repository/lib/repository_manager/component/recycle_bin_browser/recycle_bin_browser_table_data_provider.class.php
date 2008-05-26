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
    function get_learning_objects($offset, $count, $order_property, $order_direction)
    {
    	// We always use title as second sorting parameter
    	if ($order_property != LearningObject :: PROPERTY_TITLE)
    	{
	    	$order_property = array($order_property, LearningObject :: PROPERTY_TITLE);
	    	$order_direction = array($order_direction, SORT_ASC);
    	}
    	else
    	{
    		$order_property = array($order_property);
    		$order_direction = array($order_direction);
    	}
    	return $this->get_browser()->retrieve_learning_objects(null, $this->get_condition(), $order_property, $order_direction, $offset, $count, LearningObject :: STATE_RECYCLED, true);
    }
	// Inherited
    function get_learning_object_count()
    {
    	return $this->get_browser()->count_learning_objects(null, $this->get_condition(), LearningObject :: STATE_RECYCLED, true);
    }
}
?>