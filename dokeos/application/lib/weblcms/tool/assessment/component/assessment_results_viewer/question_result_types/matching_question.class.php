<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class MatchingQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$html[] = $this->display_question();
		return implode('<br/>', $html);
	}
	
	function display_survey()
	{
		
	}
	
	function display_assignment()
	{
		$html[] = $this->display_question();
		return implode('<br/>', $html);
	}

	function add_to($formvalidator)
	{
		parent :: add_to($formvalidator);
		$answers = $this->get_answers();
		
		$formvalidator->addElement('html', '<p>'.Translation :: get('Answers').' :');
		$matches = $this->get_links($answers);
		
		foreach($answers as $answer)
		{
			$formvalidator->addElement('select', $this->get_clo_question()->get_ref().'_'.$answer['answer']->get_id(), $answer['answer']->get_description(), $this->get_values($matches));
		}
		$formvalidator->addElement('html', '</p><br/>'.Translation :: get('Matches').' :<p><br/>');
		
		$i = 1;
		foreach($this->sort($matches) as $answer)
		{
			$formvalidator->addElement('html', '('.$i.') :'.$answer['answer']->get_title().'<br/>');
			$i++;
		}
		$formvalidator->addElement('html', '</p><br />');
	}
	
	function get_links($answers)
	{
		foreach ($answers as $answer) 
		{
			$answer_id = $answer['answer']->get_id();
			$links[] = $this->get_link($answer_id);
		}
		return $links;
	}
	
	function get_values($matches) 
	{
		$num = sizeof($matches);

		for ($i = 0; $i < $num; $i++)
		{
			$match = $matches[$i];
			$match_i = $match['answer'];
			$values[$i] = $i+1;
		}
		return $values;
	}
	
	function get_link($answer_id)
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $answer_id);
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		$clo_answer = $clo_answers->next_result();
		return array('answer' => $dm->retrieve_learning_object($clo_answer->get_ref(), 'answer'), 'score' => $clo_answer->get_score(), 'display_order' => $clo_answer->get_display_order());
	}
	
	function sort($matches)
	{
		$num = count($matches);
		
		for ($pos = 0; $pos < $num; $pos++)
		{
			$largest = 0;
			$largest_pos = -1;
			for ($counter = $pos; $counter < $num; $counter++)
			{
				$display = $matches[$counter]['display_order'];
				//echo $display.'$$';
				if ($display > $largest)
				{
					$largest = $display;
					$largest_pos = $counter;
				}
			}
			//switchen
			//echo $pos.'to'.$largest_pos;
			if ($largest_pos != -1) 
			{
				$temp = $matches[$pos];
				$matches[$pos] = $matches[$largest_pos];
				$matches[$largest_pos] = $temp;
			}
		}
		//print_r($matches);
		return $matches;
	}
}
?>