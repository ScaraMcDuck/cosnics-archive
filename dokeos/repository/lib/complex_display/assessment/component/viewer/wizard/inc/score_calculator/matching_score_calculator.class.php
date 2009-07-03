<?php
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class MatchingScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();

        $answers = $this->get_question()->get_options();
        $score = 0;
        $total_weight = 0;

        foreach($user_answers as $question => $user_answer)
        {
            $correct_answer = $answers[$question]->get_match();
            if ($user_answer == $correct_answer)
            {
                $score += $answers[$question]->get_weight();
            }
            
            $total_weight += $answers[$question]->get_weight();
        }

         return $this->make_score_relative($score, $total_weight);
    }
}
?>