<?php
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class SelectScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();

        $question = $this->get_question();
        if ($question->get_answer_type() == 'radio')
        {
            $answers = $question->get_options();
            $selected = $answers[$user_answers[0]];

            if ($selected->is_correct())
            {
                return $selected->get_weight();
            }
            else
            {
                return 0;
            }
        }
        else
        {
            $answers = $question->get_options();
            $score = 0;
            
            foreach($user_answers[0] as $user_answer)
            {
            	$answer = $answers[$user_answer];
                $score += $answer->get_weight();
            }

            return $score;
        }
    }
}
?>