<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
class FillInBlanksQuestionDisplay extends ContentObjectDisplay
{

    function get_full_html()
    {
        $html = parent :: get_full_html();
        return $html;
    }

    function get_description()
    {
        $object = $this->get_content_object();
        $answer_text = $object->get_answer_text();
        $answers = $object->get_answers();

        $html = array();

        $html[] = parent :: get_description();

        if($object->get_question_type() == FillInBlanksQuestion :: TYPE_SELECT)
        {
        	$answer_select = array();
        	$answer_select[] = '<select name="answer">';
        	foreach($answers as $answer)
	        {
	        	$value = substr($answer->get_value(), 1, -1);
	        	$answer_select[] = '<option value="' . $value . '">' . $value . '</option>';
	        }
        	$answer_select[] = '</select>';
        	
        	foreach($answers as $answer)
	        {
	            $answer_text = substr_replace($answer_text, implode("\n", $answer_select), strpos($answer_text, $answer->get_value(), $answer->get_position()), strlen($answer->get_value()));
	        }
        }
        else 
        {
	        foreach($answers as $answer)
	        {
	            $replacement = str_repeat('_', strlen($answer->get_value()));
	            $answer_text = substr_replace($answer_text, $replacement, strpos($answer_text, $answer->get_value(), $answer->get_position()), strlen($answer->get_value()));
	        }	
        }

        $html[] = $answer_text;

        return implode("\n", $html);
    }
}
?>