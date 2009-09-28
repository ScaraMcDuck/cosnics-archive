<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../../content_object_table/content_object_table_data_provider.class.php';
require_once dirname(__FILE__).'/../../../content_object.class.php';
require_once dirname(__FILE__).'/../../../repository_data_manager.class.php';

class ForumTableDataProvider implements ContentObjectTableDataProvider
{
	private $forum;

    function ForumTableDataProvider($forum)
    {
    	$this->forum = $forum;
    }

    function get_content_objects($offset, $count, $order_property, $order_direction)
    {
    	$dm = RepositoryDataManager :: get_instance();
    	return $dm->retrieve_content_objects(null, $this->get_condition(), array($order_property), array($order_direction));
    }

    function get_content_object_count()
    {
    	$dm = RepositoryDataManager :: get_instance();
    	return $dm->count_content_objects(null, $this->get_condition());
    }

    private function get_condition()
    {
    	return new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $this->forum->get_id());
    }
}
?>