<?php
/**
 * @package application.lib.profiler.publisher.publication_candidate_table
 */
require_once Path :: get_repository_path(). 'lib/learning_object_table/learning_object_table_data_provider.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
/**
 * This class represents a data provider for a publication candidate table
 */
class PublicationCandidateTableDataProvider implements LearningObjectTableDataProvider
{
	/**
	 * The user id of the current active user.
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
	 * Constructor.
	 * @param int $owner The user id of the current active user.
	 * @param array $types The possible types of learning objects which can be
	 * selected.
	 * @param string $query The search query.
	 */
    function PublicationCandidateTableDataProvider($owner, $types, $query = null)
    {
    	$this->types = $types;
    	$this->owner = $owner;
    	$this->query = $query;
    }
	/*
	 * Inherited
	 */
    function get_learning_objects($offset, $count, $order_property, $order_direction)
    {
    	$dm = RepositoryDataManager :: get_instance();
    	return $dm->retrieve_learning_objects(null, $this->get_condition(), array($order_property), array($order_direction), $offset, $count);
    }
	/*
	 * Inherited
	 */
    function get_learning_object_count()
    {
    	$dm = RepositoryDataManager :: get_instance();
    	return $dm->count_learning_objects(null, $this->get_condition());
    }
	/**
	 * Gets the condition by which the learning objects should be selected.
	 * @return Condition The condition.
	 */
    private function get_condition()
    {
    	$conds = array();
    	$conds[] = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $this->owner);
    	$type_cond = array();
    	foreach ($this->types as $type)
    	{
    		$type_cond[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
    	}
    	$conds[] = new OrCondition($type_cond);
		$c = DokeosUtilities :: query_to_condition($this->query);
		if (!is_null($c))
		{
			$conds[] = $c;
		}
    	return new AndCondition($conds);
    }
}
?>