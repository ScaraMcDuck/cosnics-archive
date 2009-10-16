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
    function get_objects($offset, $count, $order_property = null)
    {
    	if (is_null($order_property))
    	{
    		$order_property = array();
    	}
    	elseif(!is_array($order_property))
    	{
    		$order_property = array($order_property);
    	}

      	// We always use title as second sorting parameter
//		$order_property[] = ContentObject :: PROPERTY_TITLE;

    	$objects = $this->get_browser()->retrieve_content_objects(null, $this->get_condition(), $order_property, $offset, $count, ContentObject :: STATE_RECYCLED, false);

    	return $objects;
    }
	// Inherited
    function get_object_count()
    {
    	return $this->get_browser()->count_content_objects(null, $this->get_condition(), ContentObject :: STATE_RECYCLED, false);
    }
}
?>