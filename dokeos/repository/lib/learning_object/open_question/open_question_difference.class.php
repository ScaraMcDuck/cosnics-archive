<?php
/**
 * @package repository.learningobject
 * @subpackage openquestion
 */
/**
 * This class can be used to get the difference between open question
 */
class OpenQuestionDifference extends LearningObjectDifference
{
	function get_difference()
	{
		$object = $this->get_object();
		$version = $this->get_version();
		
		$object_string = $object->get_question_type();
	    $version_string = $verwion->get_question_type();
		
		$td = new Difference_Engine($object_string, $version_string);
		
		return array_merge($td->getDiff(), parent :: get_difference());
	}
}
?>