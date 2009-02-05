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
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment->get_id());
		$clo_questions = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		while ($clo_question = $clo_questions->next_result())
		{
			$this->check_create_answer($datamanager, $assessment_tracker, $values, $clo_question);
		}
		if ($assessment->get_assessment_type() != Assessment :: TYPE_ASSIGNMENT)
			$assessment_tracker->set_total_score($this->calculate_score($assessment_tracker));
		else
			$assessment_tracker->set_total_score(null);
		$assessment_tracker->update();
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
			}
		}
	}
	
	function get_extra($question, $extra_value = "", $file = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		switch ($question->get_type())
		{
			default: 
				if ($extra_value != null)
					return $extra_value;
				else
					return " ";
		}
	}
	
	function get_score($question, $answer, $answer_num)
	{
		$score = (Score :: factory($answer, $question, $answer_num)->get_score());
		//echo $score.';';
		return $score;
	}
	
	static function calculate_score($assessment_tracker)
	{
		$score = 0;
		$pub = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($assessment_tracker->get_assessment_id());
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $pub->get_learning_object()->get_id());
		$questions = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		while ($question = $questions->next_result())
		{
			$q_score = self :: calculate_question_score($assessment_tracker, $question);
			$score += $q_score;
		}
		return $score;
	}
	
	static function calculate_question_score($assessment_tracker, $question)
	{
		$maxscore = self :: get_all_score($question);
		$track = new WeblcmsQuestionAttemptsTracker();
		$condition_aid = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $assessment_tracker->get_id());
		$condition_qid = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_ID, $question->get_ref());
		$condition = new AndCondition(array($condition_aid, $condition_qid));
		$question_trackers = $track->retrieve_tracker_items($condition);
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
		$question = $rdm->retrieve_learning_object($clo_question->get_ref());
		switch ($question->get_type()) {
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
				return 0;
		}
	}
}
?>