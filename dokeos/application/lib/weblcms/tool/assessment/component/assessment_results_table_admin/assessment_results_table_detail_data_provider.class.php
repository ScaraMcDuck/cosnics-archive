<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_results_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
/**
 * This class represents a data provider for a results candidate table
 */
class AssessmentResultsTableDetailDataProvider extends ObjectTableDataProvider
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
	
	private $parent;
	
	private $pid;
	/**
	 * Constructor.
	 * @param int $owner The user id of the current active user.
	 * @param array $types The possible types of learning objects which can be
	 * selected.
	 * @param string $query The search query.
	 */
    function AssessmentResultsTableDetailDataProvider($parent, $owner, $pid = null, $types = array(), $query = null)
    {
    	$this->types = $types;
    	$this->owner = $owner;
    	$this->query = $query;
    	$this->parent = $parent;
    	$this->pid = $pid;
    }
	/*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
    	$order_property = $this->get_order_property($order_property);
    	$order_direction = $this->get_order_direction($order_direction);
    	$pub = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($this->pid);
    	return $this->get_user_assessments($pub);
    }
    
    function get_user_assessments($pub) 
    {
    	$condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $pub->get_id());
    	$track = new WeblcmsAssessmentAttemptsTracker();
    	$user_assessments = $track->retrieve_tracker_items($condition);
    	foreach($user_assessments as $user_assessment)
    	{
    		$all_assessments[] = $user_assessment;
    	}
    	return $all_assessments;
    }
 
	/*
	 * Inherited
	 */
    function get_object_count()
    {
    	$pub = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($this->pid);
    	return count($this->get_user_assessments($pub));
    }
}
?>