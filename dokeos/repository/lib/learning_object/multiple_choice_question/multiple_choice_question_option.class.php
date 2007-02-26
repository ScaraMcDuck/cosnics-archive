<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
/**
 * This class represents an option in a multiple choice question.
 */
class MultipleChoiceQuestionOption {
	/**
	 * The value of the option
	 */
	private $value;
	/**
	 * Is this a correct answer to the question?
	 */
	private $correct;
	/**
	 * Creates a new option for a multiple choice question
	 * @param string $value The value of the option
	 * @param boolean $correct True if the value of this option is a correct
	 * answer to the question
	 */
    function MultipleChoiceQuestionOption($value,$correct) {
    	$this->value = $value;
    	$this->correct = $correct;
    }
    /**
     * Gets the value of this option
     * @return string
     */
    function get_value()
    {
    	return $this->value;
    }
    /**
     * Determines if this option is a correct answer
     * @return boolean
     */
    function is_correct()
    {
    	return $this->correct;
    }
}
?>