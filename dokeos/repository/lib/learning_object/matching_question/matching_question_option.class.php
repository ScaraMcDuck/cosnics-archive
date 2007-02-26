<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
/**
 * This class represents an option in a matching question.
 */
class MatchingQuestionOption {
	/**
	 * The value of the option
	 */
	private $value;
	/**
	 * Is this a correct answer to the question?
	 */
	private $match;
	/**
	 * Creates a new option for a multiple choice question
	 * @param string $value The value of the option
	 * @param int $match The index of the match corresponding to this option
	 */
    function MultipleChoiceQuestionOption($value,$match) {
    	$this->value = $value;
    	$this->match = $match;
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
     * Gets the index of the match corresponding to this option
     * @return int
     */
    function is_match()
    {
    	return $this->match;
    }
}
?>