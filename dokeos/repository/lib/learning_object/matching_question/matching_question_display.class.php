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
		$html = parent::get_description();
		$options = $this->get_learning_object()->get_options();
		$html .= '<ul>';
		foreach($options as $index => $option)
		{
			$html .= '<li>'.$option->get_value().'</li>';
		}
		$html .= '</ul>';
		return $html;
	}
}
?>