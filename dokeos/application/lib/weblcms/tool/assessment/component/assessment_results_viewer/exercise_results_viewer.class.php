<?php
require_once dirname(__FILE__).'/results_viewer.class.php';
require_once dirname(__FILE__).'/question_result.class.php';

class ExerciseResultsViewer extends ResultsViewer
{
	
	function to_html()
	{
		$assessment = parent :: get_assessment();
		$assessment_id = $assessment->get_id();
		
		$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_img_path(). 'learning_object/' .$assessment->get_icon_name().'.png);">';
		$html[] = '<div class="title" style="font-size: 14px">';
		$html[] = Translation :: get('View exercise results').': '.$assessment->get_title();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $assessment->get_description();
		$html[] = '</div>';
		$html[] = '</div>';
		
		//$html[] = Translation :: get('View exercise results').': '.$assessment->get_title().'<br/><br/>'.Translation :: get('Description').':'.$assessment->get_description();
		$uaid = parent :: get_user_assessment()->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$db = WeblcmsDataManager :: get_instance();
		
		$condition = new EqualityCondition(UserQuestion :: PROPERTY_USER_ASSESSMENT_ID, $uaid);
		$user_questions = $db->retrieve_user_questions($condition);
		while ($user_question = $user_questions->next_result())
		{
			$max_total_score += $user_question->get_weight();
			$question_result = QuestionResult :: create_question_result($user_question);
			$html[] = $question_result->display_exercise();
		}
		$pct_score = round((parent :: get_user_assessment()->get_total_score() / $max_total_score) * 10000) / 100;
		$html[] = '<br/><h3>'.Translation :: get('Total score').': '.parent :: get_user_assessment()->get_total_score()."/".$max_total_score.' ('.$pct_score.'%)</h3>';
		return $html;
	}
}
?>