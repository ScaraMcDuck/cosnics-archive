<?php
/**
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class AssessmentDisplayComponent extends ComplexDisplayComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Assessment', $component_name, $builder);
	}
	
	function save_answer($complex_question_id, $answer, $score)
	{
		return $this->get_parent()->save_answer($complex_question_id, $answer, $score);
	}
	
	function finish_assessment($total_score)
	{
		return $this->get_parent()->finish_assessment($total_score);
	}
	
	function change_answer_data($complex_question_id, $score, $feedback)
	{
		return $this->get_parent()->change_answer_data($complex_question_id, $score, $feedback);
	}
}

?>
