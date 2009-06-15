<?php

require_once dirname(__FILE__) . '/../question_display.class.php';

class SelectQuestionDisplay extends QuestionDisplay
{

    function add_question_form($formvalidator)
    {
        $clo_question = $this->get_clo_question();
        $question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
        $options = $question->get_options();
        $type = $question->get_answer_type();
        $question_id = $clo_question->get_id();
        
        foreach($options as $option)
        {
        	$answers[] = $option->get_value();
        }
        
    	if ($type == 'checkbox')
        {
        	$formvalidator->addElement('advmultiselect', $question_id .'_0', '', $answers, array('style' => 'width: 20em; height: 200px'));
        }
        else
        {
        	 $formvalidator->addElement('select', $question_id .'_0', '', $answers, 'style="width: 200px;"');
        }
    }
    
	function get_instruction()
	{
		
	}
}
?>