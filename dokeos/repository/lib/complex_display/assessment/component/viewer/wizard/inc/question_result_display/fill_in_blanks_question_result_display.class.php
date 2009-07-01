<?php

require_once dirname(__FILE__).'/../question_result_display.class.php';

class FillInBlanksQuestionResultDisplay extends QuestionResultDisplay
{
	function display_question_result()
	{
		$answer_text = $this->get_question()->get_answer_text();
		$answer_text = nl2br($answer_text);
		
		$matches = array();
		preg_match_all('/\[[a-zA-Z0-9_-\s]*\]/', $answer_text, $matches);
		$matches = $matches[0];
		$answers = $this->get_answers();
		
		foreach($matches as $i => $match)
		{
			$correct_answer = substr($match, 1, strlen($match) - 2);
			
			if($answers[$i] == null) 
			{
				$answers[$i] = Translation :: get('NoAnswer');
			}
			
			if($correct_answer == $answers[$i])
			{
				$my_answer = '<span style="color: green;" >' . $answers[$i] . '</span>';
			}
			else 
			{
				$my_answer = '<span style="color: red;" >' . $answers[$i] . '</span>';
			}
			
			$answer_text = str_replace($match, $my_answer . ' / <span style="color: blue" >' . $correct_answer . '</span>', $answer_text);
		}
		
		$html[] = '<div style="border-left: 1px solid #B5CAE7; border-right: 1px solid#B5CAE7; padding: 10px;">';
		$html[] = $answer_text . '</div>';
		
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox"></th>';
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
		
        $correct_answers = $this->get_question()->get_answers();
        $i = 0;
        $feedback = 0;
        foreach($correct_answers as $correct_answer)
        {
        	if($correct_answer->get_comment() != null)
        	{
        		$html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
        		$html[] = '<td>' . ($i + 1) . '</td>';
        		$html[] = '<td>' . $correct_answer->get_value() . '</td>';
        		$html[] = '<td>' . $correct_answer->get_comment() . '</td></tr>';
        		$feedback++;
        	}
        		
        	$i++;
        }
        
        if($feedback == 0)
        {
        	$html[] = '<tr><td colspan="3" style="text-align: center;">' . Translation :: get('NoFeedback') . '</td></tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
		echo implode("\n", $html);
	}
	
	function add_borders()
	{
		return false;
	}
	
}
?>