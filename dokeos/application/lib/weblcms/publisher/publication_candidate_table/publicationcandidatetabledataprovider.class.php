<?php
require_once dirname(__FILE__).'/../../../../../repository/lib/learning_object_table/learningobjecttabledataprovider.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learningobject.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/condition/orcondition.class.php';

class PublicationCandidateTableDataProvider implements LearningObjectTableDataProvider
{
	private $owner;
	
	private $types;
	
    function PublicationCandidateTableDataProvider($owner, $types)
    {
    	$this->types = $types;
    	$this->owner = $owner;
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
    	$owner_cond = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $this->owner);
    	$type_cond = array();
    	foreach ($this->types as $type)
    	{
    		$type_cond[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
    	}
    	$type_cond = new OrCondition($type_cond);
    	$cond = new AndCondition($owner_cond, $type_cond);
    	return $cond;
    }
}
?>