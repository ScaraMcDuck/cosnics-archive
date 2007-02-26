<?php
class MultipleChoiceQuestionOption {
	private $value;
	private $correct;
    function MultipleChoiceQuestionOption($value,$correct) {
    	$this->value = $value;
    	$this->correct = $correct;
    }
    function get_value()
    {
    	return $this->value;
    }
}
?>