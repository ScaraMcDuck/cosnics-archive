<?php
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class FillInBlanksScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $blanks = $this->get_question()->get_answers();
        $answers = $this->get_answer();

        $score = 0;
        $total_weight = 0;

        foreach ($blanks as $i => $blank)
        {
            if ($blank->get_value() == '[' . $answers[$i] . ']')
            {
                $score += $blank->get_weight();
            }
            
            $total_weight += $blank->get_weight();
        }

        return $this->make_score_relative($score, $total_weight);
    }
}
?>