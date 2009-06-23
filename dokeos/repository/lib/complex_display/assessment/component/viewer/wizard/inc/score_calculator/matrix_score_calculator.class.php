<?php
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class MatrixScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();
        $question = $this->get_question();

        dump($user_answers);
        exit;

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