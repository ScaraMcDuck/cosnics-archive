<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
require_once dirname(__FILE__).'/../../../learning_object_table/learning_object_table_data_provider.class.php';
require_once dirname(__FILE__).'/../../../learning_object.class.php';
require_once dirname(__FILE__).'/../../../repository_data_manager.class.php';

class LearningPathTableDataProvider implements LearningObjectTableDataProvider
{
	private $learningpath;

	private $type;

    function LearningPathTableDataProvider($learningpath, $chapters = false)
    {
    	$this->learningpath = $learningpath;
    	$this->type = ($chapters ? 'learning_path_chapter' : 'learning_path_item');
    }

    function get_learning_objects($offset, $count, $order_property, $order_direction)
    {
    	$dm = RepositoryDataManager :: get_instance();
    	return $dm->retrieve_learning_objects($this->type, $this->get_condition(), array($order_property), array($order_direction));
    }

    function get_learning_object_count()
    {
    	$dm = RepositoryDataManager :: get_instance();
    	return $dm->count_learning_objects($this->type, $this->get_condition());
    }

    private function get_condition()
    {
    	return new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $this->learningpath->get_id());
    }
}
?>