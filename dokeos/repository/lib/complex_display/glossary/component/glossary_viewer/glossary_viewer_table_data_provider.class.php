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
class GlossaryViewerTableDataProvider extends ObjectTableDataProvider
{
	/**
	 * The user id of the current active user.
	 */
	private $owner;
	
	private $parent;
	
	private $lo;
	/**
	 * Constructor.
	 * @param int $owner The user id of the current active user.
	 * @param array $types The possible types of learning objects which can be
	 * selected.
	 * @param string $query The search query.
	 */
    function GlossaryViewerTableDataProvider($parent, $owner, $pid = null)
    {
    	$this->owner = $owner;
    	$this->parent = $parent;
        $this->lo = WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication($pid)->get_learning_object()->get_id();
    }
	/*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
    	$order_property = $this->get_order_property($order_property);
    	$order_direction = $this->get_order_direction($order_direction);
    	$dm = RepositoryDataManager :: get_instance();
    	
    	return ($dm->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->lo)));
    }
    
    function get_object_count()
    {
    	$dm = RepositoryDataManager :: get_instance();
    	$count = $dm->count_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->lo));
    	return $count;
    	
    }
    

}
?>