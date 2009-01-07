<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class MatchingQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$formvalidator->addElement('html', parent :: display_header());
		$answers = $this->get_answers();
		
		$formvalidator->addElement('html', '<p><b>'.Translation :: get('Answers').' :</b>');
		$matches = $this->get_links($answers);
		
		foreach($answers as $answer)
		{
			$name = $this->get_clo_question()->get_ref().'_'.$answer['answer']->get_id();
			$formvalidator->addElement('select', $name, $answer['answer']->get_description(), $this->get_values($matches));
		}
		
		$formvalidator->addElement('html', '</p>');
		$formvalidator->addElement('html', $this->display_footer());
	}
	
	function get_links($answers)
	{
		foreach ($answers as $answer) 
		{
			$answer_id = $answer['answer']->get_id();
			$link = $this->get_link($answer_id);
			$links = $this->check_links($links, $link);
		}
		return $links;
	}
	
	function check_links($links, $link) 
	{
		$exists = false;
		foreach ($links as $existing_link)
		{
			if ($link['answer']->get_id() == $existing_link['answer']->get_id())
				$exists = true;
		}
		if (!$exists)
			$links[] = $link;
			
		return $links;
	}
	
	function get_values($matches) 
	{
		$num = sizeof($matches);

		for ($i = 0; $i < $num; $i++)
		{
			$match = $matches[$i];
			$match_i = $match['answer'];
			$values[$match_i->get_id()] = $match_i->get_title();
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