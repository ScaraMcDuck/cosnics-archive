<?php

require_once dirname(__FILE__) . '/inc/score_calculator.class.php';

class AssessmentViewerWizardProcess extends HTML_QuickForm_Action
{
	private $parent;

	public function AssessmentViewerWizardProcess($parent)
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
			ComplexLearningObjectItem :: PROPERTY_PARENT, $this->parent->get_assessment()->get_id()));

		while($question_cloi = $questions_cloi->next_result())
		{
			$question = $rdm->retrieve_learning_object($question_cloi->get_ref());
			$score_calculator = ScoreCalculator :: factory($question, $values[$question_cloi->get_id()]);
			$score = $score_calculator->calculate_score();
			dump($question);
			echo 'score: ' . $score . '<br />';
		}

	}
}
?>