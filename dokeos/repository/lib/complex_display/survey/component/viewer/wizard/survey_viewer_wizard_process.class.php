<?php

require_once dirname(__FILE__) . '/inc/score_calculator.class.php';
require_once dirname(__FILE__) . '/inc/question_result_display.class.php';

class SurveyViewerWizardProcess extends HTML_QuickForm_Action
{
	private $parent;

	public function SurveyViewerWizardProcess($parent)
	{
		$this->parent = $parent;
	}

	function perform($page, $actionName)
	{
		foreach($_POST as $key => $value)
		{
			$value = Security :: remove_XSS($value);
			$split_key = split('_', $key);
			$question_id = $split_key[0];

			if(is_numeric($question_id))
			{
				$answer_index = $split_key[1];
				$values[$question_id][$answer_index] = $value;
			}
		}

		//$question_numbers = $_SESSION['questions'];

		$rdm = RepositoryDataManager :: get_instance();

		$questions_cloi = $rdm->retrieve_complex_learning_object_items(new EqualityCondition(
			ComplexLearningObjectItem :: PROPERTY_PARENT, $this->parent->get_survey()->get_id()));

		while($question_cloi = $questions_cloi->next_result())
		{	
			$answers = $values[$question_cloi->get_id()];
			$this->parent->get_parent()->save_answer($question_cloi->get_id(), serialize($answers));
		}
		
		echo $this->parent->get_survey()->get_finishing_text();

	}
}
?>