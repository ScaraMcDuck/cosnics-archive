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
		
		$options = $object->get_answers();
		$answer = '';
		foreach($options as $index => $option)
		{
			$answer .= '<b>' . ($index + 1) . ')</b> ' . $option->get_value() . '<br />';
		}
		
		$answer = preg_replace('/\[[^]]+\]/','___________',$answer);
		$answer = str_replace('<p>', '', $answer);
		$answer = str_replace('</p>', '', $answer);
		$html .= $answer;
		return $html;
	}
}
?>