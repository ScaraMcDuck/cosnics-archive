<?php
require_once dirname(__FILE__).'/../question_qti_import.class.php';

class MatchingQuestionQtiImport extends QuestionQtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		
		//$question_type = Question :: TYPE_MATCHING;
		$question = new MatchingQuestion();
		$title = $data['title'];
		$descr = $data['itemBody']['matchInteraction']['prompt'];
		$question->set_title($title);
		$question->set_description($description);
		//echo 'Matching question<br/>'.$question_type.'<br/>Title: '.$title.'<br/>Description: '.$descr;
		//$question = parent :: create_question($title, $descr, $question_type);
		
		$this->create_answers($data, $question);
		parent :: create_question($question);
		return $question->get_id();
	}
	
	function create_answers($data, $question)
	{
		//get matching and scores
		$matchvalues = $data['responseDeclaration']['correctResponse']['value'];
		foreach ($matchvalues as $matchvalue)
		{
			$parts = split(' ', $matchvalue);
			$matches[$parts[0]]['match'] = $parts[1];
			//print_r($matches);
		}
		
		$matchscores = $data['responseDeclaration']['mapping']['mapEntry'];
		foreach ($matchscores as $matchscore)
		{
			$parts = split(' ', $matchscore['mapKey']);
			$matches[$parts[0]]['score'] = $matchscore['mappedValue'];
			//print_r($matches);
		}
		
		//get actual answers
		$matchsets = $data['itemBody']['matchInteraction']['simpleMatchSet'];
		foreach ($matchsets as $matchset)
		{
			//print_r($matchset);
			$answers = $matchset['simpleAssociableChoice'];
			foreach ($answers as $answer)
			{
				//print_r($answer);
				$question_answers[$answer['identifier']] = $answer['_content'];
			}
		}
		//print_r($question_answers);
		
		//create answers and complex answers
		foreach ($matches as $id => $match)
		{
			$answer_title = $question_answers[$id];
			//echo $answer_title.'<br/>';
			$match_index = $this->check_match($question, $question_answers[$match['match']]);
			$opt = new MatchingQuestionOption($answer_title, $match_index, $match['score']);
			$question->add_option($opt);
			
			//$answer = $this->create_answer($answer_title);
			//$this->create_complex_answer($question, $answer, $match['score']);
			//$answer_match_title = $question_answers[$match['match']];
			//echo $answer_match_title.'<br/>';
			//$answer_match = $this->create_answer($answer_match_title);
			//$this->create_complex_answer($answer, $answer_match, 1, rand(0, count($question_answers)));
		}
	}
	
	function check_match($question, $match)
	{
		$matches = $question->get_matches();
		foreach($matches as $i => $qmatch)
		{
			if ($match == $qmatch)
			{
				return $i;
				$found = true;
			}
		}
		if (!$found)
		{
			$question->add_match($match);
			return count($question->get_matches())-1;
		}
	}
}
?>