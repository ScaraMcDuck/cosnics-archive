<?php

require_once dirname(__FILE__).'/../assessment_display.class.php';
require_once dirname(__FILE__).'/../assessment_display_component.class.php';
require_once dirname(__FILE__).'/result_viewer/question_result_display.class.php';

class AssessmentDisplayAssessmentResultViewerComponent extends AssessmentDisplayComponent 
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$rdm = RepositoryDataManager :: get_instance();
		
		$results = $this->get_parent()->get_parent()->retrieve_assessment_results();
		$question_cids = array_keys($results);
		
		$condition = new InCondition(ComplexLearningObjectItem :: PROPERTY_ID, $question_cids);
		$questions_cloi = $rdm->retrieve_complex_learning_object_items($condition);

		$total_score = 0;
		$total_weight = 0;
		$question_number = 1;
		
		while($question_cloi = $questions_cloi->next_result())
		{
			$result = $results[$question_cloi->get_id()];
			
			$question = $rdm->retrieve_learning_object($question_cloi->get_ref());
			$answers = unserialize($result['answer']);
			$question_cloi->set_ref($question);

			$score = $result['score'];
			$score = round($score * 100 ) / 100;
			
			$total_score += $score;
			$total_weight += $question_cloi->get_weight();
			
			$display = QuestionResultDisplay :: factory($question_cloi, $question_number, $answers, $score);
			$display->display();
			
			$question_number++;
			
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
	}	
}
?>