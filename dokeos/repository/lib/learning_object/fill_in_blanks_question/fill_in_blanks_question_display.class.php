<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
class FillInBlanksQuestionDisplay extends LearningObjectDisplay
{

    function get_full_html()
    {
        $html = parent :: get_full_html();
        return $html;
    }

    function get_description()
    {
        $object = $this->get_learning_object();
        $answer_text = $object->get_answer_text();
        $answers = $object->get_answers();

        $html = array();

        $html[] = parent :: get_description();

        foreach($answers as $answer)
        {
            $replacement = str_repeat('_', strlen($answer->get_value()));
            $answer_text = substr_replace($answer_text, $replacement, strpos($answer_text, $answer->get_value(), $answer->get_position()), strlen($answer->get_value()));
        }

        $html[] = $answer_text;

        return implode("\n", $html);
    }
}
?>