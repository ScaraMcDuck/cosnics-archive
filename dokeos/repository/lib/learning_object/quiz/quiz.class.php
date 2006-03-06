<?php
require_once dirname(__FILE__) . '/../../learningobject.class.php';

class Quiz extends LearningObject {
	function get_sound () {
		return $this->get_additional_property('sound');
	}
	function set_sound ($sound) {
		return $this->set_additional_property('sound', $sound);
	}
	function get_quiz_type () {
		return $this->get_additional_property('quiz_type');
	}
	function set_quiz_type ($quiz_type) {
		return $this->set_additional_property('quiz_type', $quiz_type);
	}
	function get_random () {
		return $this->get_additional_property('random');
	}
	function set_random ($random) {
		return $this->set_additional_property('random', $random);
	}
}
?>