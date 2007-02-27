<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
class MultipleChoiceQuestionDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$html = parent :: get_full_html();
		return $html;
	}
	function get_description()
	{
		$lo = $this->get_learning_object();
		$html = parent::get_description();
		$options = $lo->get_options();
		$type = $lo->get_answer_type();
		foreach($options as $index => $option)
		{
			$html .= '<input type="'.$type.'" name="option[]"/>'.$option->get_value().'<br />';
		}
		return $html;
	}
}
?>