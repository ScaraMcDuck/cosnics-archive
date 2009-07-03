<?php
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class OrderingScoreCalculator extends ScoreCalculator
{
    function calculate_score()
    {
        $user_answers = $this->get_answer();
    	
    	$answers = $this->get_question()->get_options();
    	
    	$score = 0;
    	$total_weight = 0;
    	
    	foreach($answers as $i => $answer)
    	{
    		if($user_answers[$i + 1] == $answer->get_order())
    		{
    			$score++;
    			//$score += $answer->get_weight();	    	
    		}
    		
    		$total_weight++;
    	}
    	
    	return $this->make_score_relative($score, $total_weight);
    }
}
?>