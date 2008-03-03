<?php
/**
 * $Id$
 * @package application.personal_calendar
 * @subpackage publisher
 */
require_once dirname(__FILE__).'/../../../../../../repository/lib/learning_object_table/learningobjecttabledataprovider.class.php';
require_once dirname(__FILE__).'/../../../../../../repository/lib/learningobject.class.php';
require_once dirname(__FILE__).'/../../../../../../repository/lib/repositorydatamanager.class.php';
require_once Path :: get_library_path().'condition/equalitycondition.class.php';
require_once Path :: get_library_path().'condition/andcondition.class.php';
require_once Path :: get_library_path().'condition/orcondition.class.php';
/**
 * This class represents a data provider for a publication candidate table
 */
class PublisherTableDataProvider implements LearningObjectTableDataProvider
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
    function PublisherTableDataProvider($owner, $types, $query = null)
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
    	$conds[] =  new EqualityCondition(LearningObject :: PROPERTY_TYPE, 'calendar_event');
    	$c = RepositoryUtilities :: query_to_condition($this->query);
		if (!is_null($c))
		{
			$conds[] = $c;
		}
    	$condition = new AndCondition($conds);
    	return $condition;
    }
}
?>