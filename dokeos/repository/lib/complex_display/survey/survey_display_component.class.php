<?php
/**
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class SurveyDisplayComponent extends ComplexDisplayComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Survey', $component_name, $builder);
	}
	
	function save_answer($complex_question_id, $answer)
	{
		return $this->get_parent()->save_answer($complex_question_id, $answer);
	}
}

?>
