<?php

require_once dirname(__FILE__) . '/inc/score_calculator.class.php';
require_once dirname(__FILE__) . '/inc/question_result_display.class.php';

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

		$question_number = 1;
		$total_score = 0;
		$total_weight = 0;
			
		while($question_cloi = $questions_cloi->next_result())
		{
			$question = $rdm->retrieve_learning_object($question_cloi->get_ref());
			$answers = $values[$question_cloi->get_id()];
			$question_cloi->set_ref($question);
			
			$score_calculator = ScoreCalculator :: factory($question, $answers, $question_cloi->get_weight());
			$score = $score_calculator->calculate_score();
			$total_score += $score;
			$total_weight += $question_cloi->get_weight();
			
			$display = QuestionResultDisplay :: factory($question_cloi, $question_number, $answers, $score);
			$display->display();
			
			$question_number++;
			
			$this->parent->get_parent()->save_answer($question_cloi->get_id(), serialize($answers), $score);
			
		}
		
		$html[] = '<div class="question">';
		$html[] = '<div class="title">';
		$html[] = '<div class="text">';
		$html[] = '<div class="bevel" style="float: left;">';
		$html[] = Translation :: get('TotalScore');
		$html[] = '</div>';
		$html[] = '<div class="bevel" style="text-align: right;">';
		
		$percent = round(($total_score / $total_weight) * 100 );
		 
		$html[] =  $total_score . ' / ' . $total_weight . ' (' . $percent . '%)';
		$html[] = '</div>';

		$html[] = '</div></div></div>';
		$html[] = '<div class="clear"></div>';
		
		echo implode("\n", $html);
		
		$this->parent->get_parent()->finish_assessment($percent);

	}
}
?>