<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
class MatchingQuestionDisplay extends LearningObjectDisplay
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
		$html .= '<table><tr><td><ol>';
		foreach($options as $index => $option)
		{
			$html .= '<li>'.$option->get_value().'</li>';
		}
		$html .= '</ol></td>';
		$matches = $this->get_learning_object()->get_matches();
		$html .= '<td><ol type="A">';
		foreach($matches as $index => $match)
		{
			$html .= '<li>'.$match.'</li>';
		}
		$html .= '</ol></td></tr></table>';
		return $html;
	}
}
?>