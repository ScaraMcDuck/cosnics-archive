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
		$values = $this->parent->exportValues();
		dump($values);
		//$question_numbers = $_SESSION['questions'];
		
		$rdm = RepositoryDataManager :: get_instance();
		
		$questions_cloi = $rdm->retrieve_complex_learning_object_items(new EqualityCondition(
			ComplexLearningObjectItem :: PROPERTY_PARENT, $this->parent->get_assessment()->get_id()));
			
		while($question_cloi = $questions_cloi->next_result())
		{
			$question = $rdm->retrieve_learning_object($question_cloi->get_ref());
			$answer = '';
			$score_calculator = ScoreCalculator :: factory($question, $answer);
			$score = $score_calculator->calculate_score();
			dump($question);
			echo 'score: ' . $score . '<br />';
		}
		
	}
}
?>