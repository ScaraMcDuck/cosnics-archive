<?php
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class RatingScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();
        $question = $this->get_question();

        if ($question->get_correct() == $user_answers[0])
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }
}
?>