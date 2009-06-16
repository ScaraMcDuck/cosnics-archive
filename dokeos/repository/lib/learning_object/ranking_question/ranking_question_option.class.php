<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
/**
 * This class represents an option in a ranking question.
 */
class RankingQuestionOption
{
    /**
     * The value of the option
     */
    private $value;
    /**
     * The rank of the option
     */
    private $rank;

    /**
     * Creates a new option for a ranking question
     * @param string $value The value of the option
     * @param int $rank The rank of this answer in the question
     */
    function RankingQuestionOption($value, $rank)
    {
        $this->value = $value;
        $this->rank = $rank;
    }

    /**
     * Gets the rank of this option
     * @return int
     */
    function get_rank()
    {
        return $this->rank;
    }

    /**
     * Gets the value of this option
     * @return string
     */
    function get_value()
    {
        return $this->value;
    }
}
?>