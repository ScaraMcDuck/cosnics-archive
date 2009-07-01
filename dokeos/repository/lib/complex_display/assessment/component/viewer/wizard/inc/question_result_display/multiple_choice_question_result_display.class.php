<?php

require_once dirname(__FILE__).'/../question_result_display.class.php';

class MultipleChoiceQuestionResultDisplay extends QuestionResultDisplay
{
	function display_question_result()
	{		
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation :: get('Choice') . '</th>';
        $html[] = '<th>' . Translation :: get('Correct') . '</th>';
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
		
        $answers = $this->get_answers();
        $options = $this->get_question()->get_options();
        $type = $this->get_question()->get_answer_type();
      
        foreach($options as $i => $option)
        {
       		$html[] = '<tr>';
       		
       		if(array_key_exists($i + 1, $answers))
       		{
       			$selected = " checked ";
       		}
       		else
       		{
       			$selected = "";
       		}
       		
       		if($type == 'radio')
       		{	
       			$html[] = '<td>' . '<input type="radio" name="yourchoice" value="' . $i . '" disabled' . $selected . '/>' . '</td>';
       		}
       		else
       		{
       			$html[] = '<td>' . '<input type="checkbox" name="correctchoice_' . $i . '" disabled' . $selected . '/>' . '</td>';
       		}
       		
       		if($option->is_correct())
       		{
       			$selected = " checked ";
       		}
       		else
       		{
       			$selected = "";
       		}

       		if($type == 'radio')
       		{
       			$html[] = '<td>' . '<input type="radio" name="correctchoice" value="' . $i . '" disabled' . $selected . '/>' . '</td>';
       		}
       		else 
       		{
       			$html[] = '<td>' . '<input type="checkbox" name="correctchoice_' . $i . '" disabled' . $selected . '/>' . '</td>';
       		}
       		
       		
       		$html[] = '<td>' . $option->get_value() . '</td>';
       		$html[] = '<td>' . $option->get_comment() . '</td>';
       		$html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
		echo implode("\n", $html);
	}	
}
?>