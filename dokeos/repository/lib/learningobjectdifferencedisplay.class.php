<?php

class LearningObjectDifferenceDisplay {

    
    protected function LearningObjectDifferenceDisplay($difference)
	{
		$this->diffeence = $difference;
	}
    
    function factory(&$object)
	{
		$type = $object->get_type();
		$class = LearningObject :: type_to_class($type).'Display';
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'.differencedisplay.class.php';
		return new $class($object);
	}
    
}
?>