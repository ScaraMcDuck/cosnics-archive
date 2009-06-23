<?php
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class MatchingScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $answers = $this->get_question()->get_options();
        $matches = $this->get_question()->get_matches();
        $correct = $answers[$this->get_answer_num()];
        $answer = $matches[$this->get_answer()];
        $match = $matches[$correct->get_match()];

        if ($answer == $match)
        {
            return $correct->get_weight();
        }
        else
        {
            return 0;
        }
    }
}
?>