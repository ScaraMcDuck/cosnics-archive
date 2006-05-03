<?php
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttabledataprovider.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';
require_once dirname(__FILE__).'/../../../repositorydatamanager.class.php';

class LearningPathTableDataProvider implements LearningObjectTableDataProvider
{
	private $learningpath;
	
    function LearningPathTableDataProvider($learningpath)
    {
    	$this->learningpath = $learningpath;
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
    	return new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $this->learningpath->get_id());
    }
}
?>