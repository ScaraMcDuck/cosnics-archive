<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttabledataprovider.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';
require_once dirname(__FILE__).'/../../../repositorydatamanager.class.php';

class ForumTableDataProvider implements LearningObjectTableDataProvider
{
	private $forum;

    function ForumTableDataProvider($forum)
    {
    	$this->forum = $forum;
    }

    function get_learning_objects($offset, $count, $order_property, $order_direction)
    {
    	$dm = RepositoryDataManager :: get_instance();
    	return $dm->retrieve_learning_objects(null, $this->get_condition(), array($order_property), array($order_direction));
    }

    function get_learning_object_count()
    {
    	$dm = RepositoryDataManager :: get_instance();
    	return $dm->count_learning_objects(null, $this->get_condition());
    }

    private function get_condition()
    {
    	return new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $this->forum->get_id());
    }
}
?>