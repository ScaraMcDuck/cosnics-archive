<?php
require_once dirname(__FILE__).'/results_viewer.class.php';
require_once dirname(__FILE__).'/question_result.class.php';

class ExerciseResultsViewer extends ResultsViewer
{
	
	function to_html()
	{
		$assessment = parent :: get_assessment();
		$assessment_id = $assessment->get_id();
		
		echo '<div class="learning_object" style="background-image: url('. Theme :: get_common_img_path(). 'learning_object/' .$assessment->get_icon_name().'.png);">';
		echo '<div class="title" style="font-size: 14px">';
		echo Translation :: get('View exercise results').': '.$assessment->get_title();
		echo '</div>';
		echo '<div class="description">';
		echo $assessment->get_description();
		echo '</div>';
		echo '</div>';
		
		//echo Translation :: get('View exercise results').': '.$assessment->get_title().'<br/><br/>'.Translation :: get('Description').':'.$assessment->get_description();
		$uaid = parent :: get_user_assessment()->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$db = WeblcmsDataManager :: get_instance();
		
		$condition = new EqualityCondition(UserQuestion :: PROPERTY_USER_ASSESSMENT_ID, $uaid);
		$user_questions = $db->retrieve_user_questions($condition);
		while ($user_question = $user_questions->next_result())
		{
			$question_result = QuestionResult :: create_question_result($this, $user_question, $this->get_edit_rights());
			$question_result->display_exercise();
		}
		echo $this->toHtml();
		$max_total_score = $assessment->get_maximum_score();
		$pct_score = round((parent :: get_user_assessment()->get_total_score() / $max_total_score) * 10000) / 100;
		echo '<br/><h3>'.Translation :: get('Total score').': '.parent :: get_user_assessment()->get_total_score()."/".$max_total_score.' ('.$pct_score.'%)</h3>';
		//return $html;
	}
}
?>