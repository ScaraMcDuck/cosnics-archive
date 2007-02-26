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
		$html = parent::get_description();
		$object = $this->get_learning_object();
		$answer = $object->get_answer();
		$answer = preg_replace('/\[[^]]+\]/','___________',$answer);
		$html .= $answer;
		return $html;
	}
}
?>