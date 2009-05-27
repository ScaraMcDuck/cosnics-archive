<?php
/**
 * @package application.weblcms.tool.exercise.component.exercise_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
/**
 * This class represents a data provider for a publication candidate table
 */
class WikiPageTableDataProvider extends ObjectTableDataProvider
{
	/**
	 * The id of the current publication/wiki.
	 */
	private $owner;
	/**
	 * The possible types of learning objects which can be selected.
	 */
	private $types;
	/**
	 * The search query, or null if none.
	 */
	private $query;
    /**
	 * The pagebrowser.
	 */
	private $parent;
	/**
	 * Constructor.
	 * @param int $owner The user id of the current active user.
	 * @param array $types The possible types of learning objects which can be
	 * selected.
	 * @param string $query The search query.
	 */
    function WikiPageTableDataProvider($parent,$owner)
    {
    	$this->parent = $parent;
        $this->owner = $owner;
    }
	/*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
    	$dm = RepositoryDataManager :: get_instance();
        $condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->owner);
        return $dm->retrieve_complex_learning_object_items($condition, $order_property, $order_direction, $offset, $count);
    }
	/*
	 * Inherited
	 */
    function get_object_count()
    {
    	return count($this->get_objects()->as_array());
    }
}
?>