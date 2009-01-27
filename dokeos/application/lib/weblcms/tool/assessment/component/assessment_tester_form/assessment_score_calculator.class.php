<?php
require_once dirname(__FILE__).'/score.class.php';
require_once Path :: get_application_path().'lib/weblcms/trackers/weblcms_question_attempts_tracker.class.php';

class AssessmentScoreCalculator 
{
	private $parent;
	
	function build_answers($values, $assessment, $datamanager, $parent)
	{
		$this->parent = $parent;
		$tracker_id = $_SESSION['assessment_tracker'];
		$tracker = new WeblcmsAssessmentAttemptsTracker();
		$condition = new EqualityCondition(MainTracker :: PROPERTY_ID, $tracker_id);
		$assessment_trackers = $tracker->retrieve_tracker_items($condition);
		$assessment_tracker = $assessment_trackers[0];
		//dump($assessment_tracker);
		//$values = $tester_form->exportValues();
		//$user_assessment = new UserAssessment();
		//$user_assessment->set_assessment_id($assessment->get_id());	
		//$user_assessment->set_user_id($this->get_user_id($assessment, $values));
		//$id = $datamanager->get_next_user_assessment_id();
		//$user_assessment->set_id($id);
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment->get_id());
		$clo_questions = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		while ($clo_question = $clo_questions->next_result())
		{
			$this->check_create_answer($datamanager, $assessment_tracker, $values, $clo_question);
		}
		$assessment_tracker->set_total_score($this->calculate_score($assessment_tracker));
		$assessment_tracker->update();
		//$user_assessment->set_total_score($this->calculate_score($user_assessment));
		//return $user_assessment;
	}
	
	function get_user_id($assessment)
	{
		if ($assessment->get_assessment_type() == Survey :: TYPE_SURVEY)
		{
			if ($assessment->get_anonymous() == true)
				return 0;
		}
		return $this->parent->get_user_id();
	}
	
	function check_create_answer($datamanager, $assessment_tracker, $values, $clo_question) 
	{
		foreach($_FILES as $key => $file)
		{
			$parts = split("_", $key);
			//create document and afterwards user answer
			if ($parts[0] == $clo_question->get_ref()) 
			{
				if ($file['name'] != null)
				{
				 	$owner = parent :: get_user_id();
				 	$filename = Filesystem :: create_unique_name(Path :: get(SYS_REPO_PATH).$owner, $file['name']);
					$path = $owner.'/'.$filename;
					$full_path = Path :: get(SYS_REPO_PATH).$path;
					move_uploaded_file($file['tmp_name'], $full_path) or die('Failed to create "'.$full_path.'"');
				 	chmod($full_path, 0777);
					$object = new Document();
					$object->set_path($path);
					$object->set_filename($filename);
					$object->set_filesize(Filesystem::get_disk_space($full_path));
					$object->set_default_property('owner', $owner);
					$object->set_title($filename);
					$object->create();
					$this->add_user_answer($datamanager, $assessment_tracker, $key, $object->get_id());
				 	return;
				}
			}
		}
		foreach($values as $key => $value)
		{
			$parts = split("_", $key);
			//create user answer
			if ($parts[0] == $clo_question->get_ref()) 
			{
				$this->add_user_answer($datamanager, $assessment_tracker, $key, $value);
			}
		}	
	}
	
	function add_user_answer($datamanager, $assessment_tracker, $key, $value)
	{
		if ($key != 'submit') {
			$parts = split("_", $key);
			if (is_numeric($parts[0])) {
				$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($parts[0]);
				$extra = $this->get_extra($question, $value, $_FILES[$key]);
				$score = $this->get_score($question, $extra, $parts[1]);
				$params = array(
					'assessment_attempt_id' => $assessment_tracker->get_id(),
					'question_id' => $parts[0],
					'answer' => $extra,
					'answer_idx' => $parts[1],
					'score' => $score,
					'feedback' => 0
				);
				$question_tracker = new WeblcmsQuestionAttemptsTracker();
				$question_track_id = $question_tracker->track($params);
				//$user_question = $this->get_question($datamanager, $user_assessment, $parts[0]);
				//$answer = new UserAnswer();
				//$answer->set_id($datamanager->get_next_user_answer_id());
				//$answer->set_user_question_id($user_question->get_id());
				//$answer->set_answer_id($parts[1]);
				//$answer->set_extra($this->get_extra($user_question, $value, $_FILES[$key]));
				//$answer->set_score($this->get_score($user_question, $answer));
				//$datamanager->create_user_answer($answer);
			}
		}
	}
	
	function get_extra($question, $extra_value = "", $file = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		switch ($question->get_type())
		{
			//case 'multiple_choice_question':
				/*$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
				$clo_answers = $rdm->retrieve_complex_learning_object_items($condition);
				while ($clo_answer = $clo_answers->next_result())
				{
					$answers[] = $clo_answer;
				}
				$clo_answer = $answers[$extra_value];
				$lo_answer = $rdm->retrieve_learning_object($clo_answer->get_ref());
				return $lo_answer->get_id();*/
				/*$options = $question->get_options();
				foreach ($options as $i => $option)
				{
					if ($i + 1 == $extra_value)
						return $option->get_value();
				}*/
			default: 
				if ($extra_value != null)
					return $extra_value;
				else
					return " ";
		}
	}
	
	/*function get_question($datamanager, $user_assessment, $question_id)
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
	}*/
	
	function get_score($question, $answer, $answer_num)
	{
		/*if ($user_answer->get_answer_id() != 0) 
		{
			$answer = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_answer->get_answer_id(), 'answer');
		}*/	
		//$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($user_question->get_question_id(), 'question');
		
		return (Score :: factory($answer, $question, $answer_num)->get_score());
	}
	
	static function calculate_score($assessment_tracker)
	{
		$score = 0;
		//$db = WeblcmsDataManager :: get_instance();
		//$condition = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $assessment_tracker->get_id());
		//$user_questions = $db->retrieve_user_questions($condition);
		//$track = new WeblcmsQuestionAttemptsTracker();
		//$question_trackers = $track->retrieve_tracker_items($condition);
		/*while ($user_question = $user_questions->next_result())
		{
			$score += self :: calculate_question_score($user_question);
		}*/
		$pub = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($assessment_tracker->get_assessment_id());
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $pub->get_learning_object()->get_id());
		$questions = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		while ($question = $questions->next_result())
		{
			$score += self :: calculate_question_score($assessment_tracker, $question);
		}
		return $score;
	}
	
	static function calculate_question_score($assessment_tracker, $question)
	{
		//$rdm = RepositoryDataManager :: get_instance();
		$maxscore = self :: get_all_score($question);
		$track = new WeblcmsQuestionAttemptsTracker();
		$condition_aid = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $assessment_tracker->get_id());
		$lo_question = RepositoryDataManager :: get_instance()->retrieve_learning_object($question->get_ref());
		$condition_qid = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_ID, $lo_question->get_id());
		$condition = new AndCondition(array($condition_aid, $condition_qid));
		$question_trackers = $track->retrieve_tracker_items($condition);
		//echo 'max'.$maxscore.'<br/>';
		//$condition = new EqualityCondition(UserAnswer :: PROPERTY_USER_QUESTION_ID, $user_question->get_id());
		//$user_answers = WeblcmsDataManager :: get_instance()->retrieve_user_answers($condition);
		$score = 0;
		foreach ($question_trackers as $q_tracker)
		{
			$score += ($q_tracker->get_score() * $question->get_weight()) / $maxscore;
		}
		return $score;
	}
	
	static function get_all_score($clo_question)
	{
		$rdm = RepositoryDataManager :: get_instance();
		//$lo_question = $rdm->retrieve_learning_object($user_question->get_question_id());
		$question = $rdm->retrieve_learning_object($clo_question->get_ref());
		switch ($question->get_type()) {
			/*case Question :: TYPE_PERCENTAGE:
				return 100;
			case Question :: TYPE_SCORE:
				$question = $rdm->retrieve_learning_object($user_question->get_question_id());
				$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
				$clo_answers = $rdm->retrieve_complex_learning_object_items($condition);
				$high = -10000;
				while ($clo_answer = $clo_answers->next_result())
				{
					if ($clo_answer->get_score() > $high)
						$high = $clo_answer->get_score();
				}
				return $high;
			case Question :: TYPE_OPEN:
				return $user_question->get_weight();
			case Question :: TYPE_OPEN_WITH_DOCUMENT:
				return $user_question->get_weight();
			case Question :: TYPE_DOCUMENT:
				return $user_question->get_weight();*/
			case 'open_question':
				return $clo_question->get_weight();
			case 'rating_question':
				return $question->get_high();
			case 'multiple_choice_question':
				$answers = $question->get_options();
				$score = 0;
				foreach ($answers as $answer)
				{
					if ($question->get_answer_type() == 'radio')
					{
						if ($answer->is_correct())
							$score += $answer->get_weight();
					}
					else
						$score += $answer->get_weight();
				}
				return $score;
			case 'matching_question':
				$answers = $question->get_options();
				$score = 0;
				foreach ($answers as $answer)
				{
					$score += $answer->get_weight();
				}
				return $score;
			case 'fill_in_blanks_question':
				$answers = $question->get_answers();
				$score = 0;
				foreach ($answers as $answer)
				{
					$score += $answer->get_weight();
				}
				return $score;
			default:
				/*$score = 0;
				//$question = $rdm->retrieve_learning_object($user_question->get_question_id());
				$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
				$clo_answers = $rdm->retrieve_complex_learning_object_items($condition);
				while ($clo_answer = $clo_answers->next_result())
				{
					$score += $clo_answer->get_score();
				}	
				return $score;*/
				return 0;
		}
	}
}
?>