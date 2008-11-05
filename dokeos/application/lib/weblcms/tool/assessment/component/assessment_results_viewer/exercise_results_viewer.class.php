<?php
require_once dirname(__FILE__).'/results_viewer.class.php';

class ExerciseResultsViewer extends ResultsViewer
{
	
	function to_html()
	{
		$assessment = parent :: get_assessment();
		$assessment_id = $assessment->get_id();
		$html[] = 'View exercise results: '.$assessment->get_title().'<br/><br/>Description:'.$assessment->get_description();
		
		$uaid = parent :: get_user_assessment()->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$db = WeblcmsDataManager :: get_instance();
		
		$condition = new EqualityCondition(UserQuestion :: PROPERTY_USER_ASSESSMENT_ID, $uaid);
		$user_questions = $db->retrieve_user_questions($condition);
		while ($user_question = $user_questions->next_result())
		{
			$html[] = $this->add_user_question($user_question);
		}
		return $html;
	}
	
	function add_user_question($user_question) {
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_question->get_question_id(), 'question');
		$html[] = 'Question:'.$question->get_title().'<br/>Description:'.$question->get_description();
		$condition = new EqualityCondition(UserAnswer :: PROPERTY_USER_QUESTION_ID, $user_question->get_id());
		$user_answers = WeblcmsDataManager :: get_instance()->retrieve_user_answers($condition);
		
		while ($user_answer = $user_answers->next_result())
		{
			$html[] = $this->add_user_answer($user_answer);
		}
		
		return implode('<br/>', $html);
	}
	
	function add_user_answer($user_answer) {
		if ($user_answer->get_answer_id() != 0) 
		{
			$answer = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_answer_id(), 'answer');
			$html[] = 'Answer:'.$answer->get_title().'<br/>Description:'.$answer->get_description();
			$html[] = 'User answer:'.$user_answer->get_extra().'<br/>Score:'.$user_answer->get_score();
		}
		else 
		{
			$html[] = 'User answer:'.$user_answer->get_extra().'<br/>Score:'.$user_answer->get_score();
		}
		return implode('<br/>', $html);
	}
}
?>