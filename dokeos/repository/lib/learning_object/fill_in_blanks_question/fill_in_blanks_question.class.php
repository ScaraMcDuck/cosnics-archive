<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
class FillInBlanksQuestion extends LearningObject {
	const PROPERTY_ANSWER = 'answer';

	function get_answer () {
		return $this->get_additional_property(self :: PROPERTY_ANSWER);
	}
	function set_answer ($answer) {
		return $this->set_additional_property(self :: PROPERTY_ANSWER, $answer);
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_ANSWER);
	}
}
?>