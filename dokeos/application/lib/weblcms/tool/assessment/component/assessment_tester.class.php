<?php
/**
 * @package application.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__).'/assessment_tester_form/assessment_tester_form.class.php';
require_once dirname(__FILE__).'/assessment_tester_form/score.class.php';

class AssessmentToolTesterComponent extends AssessmentToolComponent
{
	private $questions;
	
	function run()
	{
		$this->questions = null;
		$datamanager = WeblcmsDataManager :: get_instance();
		
		$pid = $_GET[Tool :: PARAM_PUBLICATION_ID];
		$pub = $datamanager->retrieve_learning_object_publication($pid);
		$visible = !$pub->is_hidden() && $pub->is_visible_for_target_users();
		
		if (!$this->is_allowed(VIEW_RIGHT) || !$visible)
		{
			Display :: display_not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		$assessment = $pub->get_learning_object();
		
		$tester_form = new AssessmentTesterForm($assessment, $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $pid)));
		if (!$tester_form->validate()) 
		{
			$this->display_header($trail);
			$this->action_bar = $this->get_toolbar();
			echo $this->action_bar->as_html();
			
			echo $tester_form->toHtml();
			$this->display_footer();
		} 
		else
		{
			$user_assessment = $this->build_answers($tester_form, $assessment, $datamanager);
			$user_assessment->set_total_score($this->calculate_score($user_assessment));
			WeblcmsDataManager :: get_instance()->create_user_assessment($user_assessment);
			$params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id());
			$this->redirect(null, null, false, $params);
		}
	}
	
	function build_answers($tester_form, $assessment, $datamanager)
	{
		$values = $tester_form->exportValues();
		$user_assessment = new UserAssessment();
		$user_assessment->set_assessment_id($assessment->get_id());
		$user_assessment->set_user_id(parent :: get_user_id());
		$id = $datamanager->get_next_user_assessment_id();
		$user_assessment->set_id($id);
		foreach($values as $key => $value)
		{
			$this->add_user_answer($datamanager, $user_assessment, $key, $value);
		}
		return $user_assessment;
	}
	
	function add_user_answer($datamanager, $user_assessment, $key, $value)
	{
		if ($key != 'submit') {
			$parts = split("_", $key);
			$user_question = $this->get_question($datamanager, $user_assessment, $parts[0]);
			$answer = new UserAnswer();
			$answer->set_id($datamanager->get_next_user_answer_id());
			$answer->set_user_question_id($user_question->get_id());
			$answer->set_answer_id($parts[1]);
			$answer->set_extra($this->get_extra($user_question, $value));
			$answer->set_score($this->get_score($user_question, $answer));
			$datamanager->create_user_answer($answer);
		}
	}
	
	function get_extra($user_question, $extra_value)
	{
		$rdm = RepositoryDataManager :: get_instance();
		$question = $rdm->retrieve_learning_object($user_question->get_question_id(), 'question');
		switch ($question->get_question_type())
		{
			case Question :: TYPE_DOCUMENT:
				$documents = $rdm->retrieve_learning_objects('document');
				while ($document = $documents->next_result())
				{
					$lo_documents[] = $document;
				}
				return $lo_documents[$extra_value]->get_id();
			case Question :: TYPE_OPEN_WITH_DOCUMENT:
				$documents = $rdm->retrieve_learning_objects('document');
				while ($document = $documents->next_result())
				{
					$lo_documents[] = $document;
				}
				return $lo_documents[$extra_value]->get_id();
			case Question :: TYPE_MULTIPLE_CHOICE:
				$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
				$clo_answers = $rdm->retrieve_complex_learning_object_items($condition);
				while ($clo_answer = $clo_answers->next_result())
				{
					$answers[] = $clo_answer;
				}
				$clo_answer = $answers[$extra_value];
				$lo_answer = $rdm->retrieve_learning_object($clo_answer->get_ref());
				return $rdm->retrieve_learning_object($clo_answer->get_ref())->get_id();
			case Question :: TYPE_MATCHING:
				$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $user_question->get_question_id());
				$clo_answers = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
				while ($clo_answer = $clo_answers->next_result())
				{
					$answers[] = $this->get_link($clo_answer->get_ref());
				}
				$sorted_answers = $this->sort($answers);
				$index = $extra_value;
				$user_answer = $sorted_answers[$index];
				return $user_answer['answer']->get_id();
			default: 
				return $extra_value;
		}
	}
	
	function get_question($datamanager, $user_assessment, $question_id)
	{
		if ($this->questions[$question_id] == null)
		{
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $question_id);
			$clo_questions = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
			$clo_question = $clo_questions->next_result();
			$user_question = new UserQuestion();
			$user_question->set_id($datamanager->get_next_user_question_id());
			$user_question->set_user_test_id($user_assessment->get_id());
			$user_question->set_question_id($question_id); 
			$user_question->set_weight($clo_question->get_weight());
			$user_question->create();
			$this->questions[$question_id] = $user_question;
		}
		return $this->questions[$question_id];
	}
	
	function get_score($user_question, $user_answer)
	{
		if ($user_answer->get_answer_id() != 0) 
		{
			$answer = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_answer_id(), 'answer');
		}	
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_question->get_question_id(), 'question');
		
		return (Score :: factory($answer, $user_answer, $user_question)->get_score());
	}
	
	static function calculate_score($user_assessment)
	{
		$score = 0;
		$db = WeblcmsDataManager :: get_instance();
		$condition = new EqualityCondition(UserQuestion :: PROPERTY_USER_ASSESSMENT_ID, $user_assessment->get_id());
		$user_questions = $db->retrieve_user_questions($condition);
		while ($user_question = $user_questions->next_result())
		{
			print_r($user_question);
			$score += self :: calculate_question_score($user_question);
		}
		return $score;
	}
	
	static function calculate_question_score($user_question)
	{
		$rdm = RepositoryDataManager :: get_instance();
		$maxscore = self :: get_all_score($user_question);
		//echo 'max'.$maxscore.'<br/>';
		$condition = new EqualityCondition(UserAnswer :: PROPERTY_USER_QUESTION_ID, $user_question->get_id());
		$user_answers = WeblcmsDataManager :: get_instance()->retrieve_user_answers($condition);
		while ($user_answer = $user_answers->next_result())
		{
			//echo 'uas'.$user_answer->get_score().'<br/>';
			//echo 'uasavg'.($user_answer->get_score() * $user_question->get_weight()) / $maxscore.'<br/>';
			//echo 'uasavg2'.($user_answer->get_score().$user_question->get_weight()).$maxscore.'<br/>';
			$score += ($user_answer->get_score() * $user_question->get_weight()) / $maxscore;
		}
		//echo 'tqs'.$score.'<br/>';
		return $score;
	}
	
	static function get_all_score($user_question)
	{
		$rdm = RepositoryDataManager :: get_instance();
		$lo_question = $rdm->retrieve_learning_object($user_question->get_question_id());
		switch ($lo_question->get_question_type()) {
			case Question :: TYPE_PERCENTAGE:
				return 100;
			case Question :: TYPE_OPEN:
				return $user_question->get_weight();
			case Question :: TYPE_OPEN_WITH_DOCUMENT:
				return $user_question->get_weight();
			case Question :: TYPE_DOCUMENT:
				return $user_question->get_weight();
			default:
				$score = 0;
				$question = $rdm->retrieve_learning_object($user_question->get_question_id());
				$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
				$clo_answers = $rdm->retrieve_complex_learning_object_items($condition);
				while ($clo_answer = $clo_answers->next_result())
				{
					//echo 'cloas'.$clo_answer->get_score().' ';
					$score += $clo_answer->get_score();
				}	
				return $score;
		}
	}
	
	//TODO: MOVE out of this class..
	function sort($matches)
	{
		$num = count($matches);
		
		for ($pos = 0; $pos < $num; $pos++)
		{
			$largest = 0;
			$largest_pos = -1;
			for ($counter = $pos; $counter < $num; $counter++)
			{
				$display = $matches[$counter]['display_order'];
				if ($display > $largest)
				{
					$largest = $display;
					$largest_pos = $counter;
				}
			}
			//switchen
			if ($largest_pos != -1) 
			{
				$temp = $matches[$pos];
				$matches[$pos] = $matches[$largest_pos];
				$matches[$largest_pos] = $temp;
			}
		}
		
		return $matches;
	}
	
	function get_link($answer_id)
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $answer_id);
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		$clo_answer = $clo_answers->next_result();
		return array('answer' => $dm->retrieve_learning_object($clo_answer->get_ref(), 'answer'), 'score' => $clo_answer->get_score(), 'display_order' => $clo_answer->get_display_order());
	}
}
?>