<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttabledataprovider.class.php';

class RepositoryBrowserTableDataProvider implements LearningObjectTableDataProvider
{
	private $browser;

	private $condition;

	function RepositoryBrowserTableDataProvider($browser, $condition)
	{
		$this->browser = $browser;
		$this->condition = $condition;
	}

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
    	return $this->browser->retrieve_learning_objects(null, $this->get_condition(), $order_property, $order_direction, $offset, $count);
    }

    function get_learning_object_count()
    {
    	return $this->browser->count_learning_objects(null, $this->get_condition());
    }

    protected function get_condition()
    {
    	return $this->condition;
    }
    
    protected function get_browser()
    {
    	return $this->browser;
    }
}
?>