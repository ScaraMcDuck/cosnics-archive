<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class MatchingQuestionDisplay extends QuestionDisplay
{
	function add_question_form($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$answers = $question->get_options();
		$dbmatches = $question->get_matches();

		$this->shuffle_with_keys($dbmatches);
		$i = 0;
		foreach ($dbmatches as $num => $match)
		{
			$matches[$num] = ($i+1);
			$match = substr($match, 3, strlen($match) - 7);
			$matchcontents[$i] = $match;
			$i++;
		}
		//dump($matches);
		$formvalidator->addElement('html', '<div style="width: 50%; float: left;">' . Translation :: get('Options') . ': <br/><ol>');
		foreach($answers as $i => $answer)
		{
			//$name = $this->get_clo_question()->get_id().'_'.$i;
			$answer_text = substr($answer->get_value(), 3, strlen($answer->get_value()) - 7);
			//$formvalidator->addElement('select', $name, $answer_text, $matches);
			$formvalidator->addElement('html', '<li>' . $answer_text . '</li>');
		}
		$formvalidator->addElement('html', '</ol></div>');

		$formvalidator->addElement('html', '<div style="width: 50%; float: right;">'.Translation :: get('Matches').': <br/><ol type="a">');
		foreach ($matchcontents as $match)
		{
			$formvalidator->addElement('html', '<li>'.$match.'</li>');
		}
		$formvalidator->addElement('html', '</ol></div><div class="clear"></div>');
	}

	function shuffle_with_keys(&$array)
	{
	    /* Auxiliary array to hold the new order */
	    $aux = array();
	    /* We work with an array of the keys */
	    $keys = array_keys($array);
	    /* We shuffle the keys */
	    shuffle($keys);
	    /* We iterate thru' the new order of the keys */
	    foreach($keys as $key) {
	      /* We insert the key, value pair in its new order */
	      $aux[$key] = $array[$key];
	      /* We remove the element from the old array to save memory */
	      unset($array[$key]);
	    }
	    /* The auxiliary array with the new order overwrites the old variable */
	    $array = $aux;
  	}

	function add_borders()
	{
		return true;
	}
}
?>