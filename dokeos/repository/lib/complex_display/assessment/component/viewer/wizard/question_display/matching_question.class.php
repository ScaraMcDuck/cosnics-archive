<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class MatchingQuestionDisplay extends QuestionDisplay
{
	function add_question_form($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$answers = $question->get_options();
		$matches = $question->get_matches();
		$renderer = $formvalidator->defaultRenderer();

        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th colspan="2">' . Translation :: get('MatchOptionAnswer') . '</th>';
//		$table_header[] = '<th></th>';
//		$table_header[] = '<th>' . Translation :: get('Options') . '</th>';
//		$table_header[] = '<th>' . Translation :: get('Matches') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));

        $question_id = $clo_question->get_id();

        $answers = $this->shuffle_with_keys($answers);

        $answer_count = 0;
		foreach($answers as $answer_id => $answer)
		{
			$answer_name = $question_id . '_' . $answer_id;

			$group = array();
			$answer_number = ($answer_count + 1) . '.';
			$group[] = $formvalidator->createElement('static', null, null, $answer_number);
			$group[] = $formvalidator->createElement('static', null, null, $answer->get_value());
			$group[] = $formvalidator->createElement('select', $answer_name, null, $this->prepare_matches($matches));

            $formvalidator->addGroup($group, 'group_' . $answer_name, null, '', false);

            $renderer->setElementTemplate('<tr class="' . ($answer_count % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'group_' . $answer_name);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'group_' . $answer_name);
            $answer_count++;
		}

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
	}

	function prepare_matches($matches)
	{
		$matches = $this->shuffle_with_keys($matches);
		return $matches;
	}

	function shuffle_with_keys($array)
	{
	    /* Auxiliary array to hold the new order */
	    $aux = array();
	    /* We work with an array of the keys */
	    $keys = array_keys($array);
	    /* We shuffle the keys */
	    shuffle($keys);
	    /* We iterate thru' the new order of the keys */
	    foreach($keys as $key)
	    {
	      /* We insert the key, value pair in its new order */
	      $aux[$key] = $array[$key];
	      /* We remove the element from the old array to save memory */
	    }
	    /* The auxiliary array with the new order overwrites the old variable */
	    return $aux;
	}

	function get_instruction()
	{
		return Translation :: get('SelectCorrectAnswers');
	}
}
?>