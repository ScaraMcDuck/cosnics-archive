<?php

require_once dirname(__FILE__) . '/../question_display.class.php';

class MultipleChoiceQuestionDisplay extends QuestionDisplay
{

    function add_question_form($formvalidator)
    {
        $clo_question = $this->get_clo_question();
        $question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
        $answers = $question->get_options();
        $type = $question->get_answer_type();
        $renderer = $formvalidator->defaultRenderer();

        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th>' . Translation :: get('SelectCorrectAnswer') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));

        $question_id = $clo_question->get_id();

        foreach ($answers as $i => $answer)
        {
            $group = array();

            if ($type == 'radio')
            {
                $answer_name = $question_id . '_0';
                $group[] = $formvalidator->createElement('radio', $answer_name, null, null, $i);
                $group[] = $formvalidator->createElement('static', null, null, $answer->get_value());
            }
            elseif ($type == 'checkbox')
            {
                $answer_name = $question_id . '_' . ($i + 1);
                $group[] = $formvalidator->createElement('checkbox', $answer_name);
                $group[] = $formvalidator->createElement('static', null, null, $answer->get_value());
            }

            $formvalidator->addGroup($group, 'option_' . $i, null, '', false);

            $renderer->setElementTemplate('<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $i);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);
        }

//        if ($type == 'radio')
//        {
//            $i = 0;
//            foreach ($answers as $answer)
//            {
//                $answer_text = substr($answer->get_value(), 3, strlen($answer->get_value()) - 7);
//                $elements[] = $formvalidator->createElement('radio', null, null, $answer_text, $i);
//                $i ++;
//            }
//            $name = $this->get_clo_question()->get_id() . '_0';
//            $formvalidator->addGroup($elements, $name, null, '<br/>');
//        }
//        else
//            if ($type == 'checkbox')
//            {
//                foreach ($answers as $i => $answer)
//                {
//                    $answer_text = substr($answer->get_value(), 3, strlen($answer->get_value()) - 7);
//                    $name = $this->get_clo_question()->get_id() . '_' . ($i + 1);
//                    $formvalidator->addElement('checkbox', $name, '', $answer_text);
//                }
//            }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
    }
    
	function add_border()
	{
		return false;
	}
}
?>