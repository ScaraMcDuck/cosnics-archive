<?php

class AssessmentScoreCalculator 
{
	private $parent;
	
	function build_answers($values, $assessment, $datamanager, $parent)
	{
		$this->parent = $parent;
		//$values = $tester_form->exportValues();
		$user_assessment = new UserAssessment();
		$user_assessment->set_assessment_id($assessment->get_id());	
		$user_assessment->set_user_id($this->get_user_id($assessment, $values));
		$id = $datamanager->get_next_user_assessment_id();
		$user_assessment->set_id($id);
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment->get_id());
		$clo_questions = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
		while ($clo_question = $clo_questions->next_result())
		{
			$this->check_create_answer($datamanager, $user_assessment, $values, $clo_question);
		}
		$user_assessment->set_total_score($this->calculate_score($user_assessment));
		return $user_assessment;
	}
	
	function get_user_id($assessment, $values)
	{
		if ($assessment->get_assessment_type() == Survey :: TYPE_SURVEY)
		{
			if ($assessment->get_anonymous() == true)
				return 0;
		}
		return $this->parent->get_user_id();
	}
	
	function check_create_answer($datamanager, $user_assessment, $values, $clo_question) 
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
				 	$filename = Filesystem::create_unique_name(Path :: get(SYS_REPO_PATH).$owner, $file['name']);
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
					$this->add_user_answer($datamanager, $user_assessment, $key, $object->get_id());
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
				$this->add_user_answer($datamanager, $user_assessment, $key, $value);
			}
		}	
	}
	
	function add_user_answer($datamanager, $user_assessment, $key, $value)
	{
		if ($key != 'submit') {
			$parts = split("_", $key);
			if (is_numeric($parts[0])) {
				$user_question = $this->get_question($datamanager, $user_assessment, $parts[0]);
				$answer = new UserAnswer();
				$answer->set_id($datamanager->get_next_user_answer_id());
				$answer->set_user_question_id($user_question->get_id());
				$answer->set_answer_id($parts[1]);
				$answer->set_extra($this->get_extra($user_question, $value, $_FILES[$key]));
				$answer->set_score($this->get_score($user_question, $answer));
				$datamanager->create_user_answer($answer);
			}
		}
	}
	
	function get_extra($user_question, $extra_value = "", $file = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		$question = $rdm->retrieve_learning_object($user_question->get_question_id(), 'question');
		switch ($question->get_question_type())
		{
			case Question :: TYPE_MULTIPLE_CHOICE:
				$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
				$clo_answers = $rdm->retrieve_complex_learning_object_items($condition);
				while ($clo_answer = $clo_answers->next_result())
				{
					$answers[] = $clo_answer;
				}
				$clo_answer = $answers[$extra_value];
				$lo_answer = $rdm->retrieve_learning_object($clo_answer->get_ref());
				return $lo_answer->get_id();
			default: 
				if ($extra_value != null)
					return $extra_value;
				else
					return " ";
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
			$score += ($user_answer->get_score() * $user_question->get_weight()) / $maxscore;
		}
		//echo $score.'<br/>';
		return $score;
	}
	
	static function get_all_score($user_question)
	{
		$rdm = RepositoryDataManager :: get_instance();
		$lo_question = $rdm->retrieve_learning_object($user_question->get_question_id());
		switch ($lo_question->get_question_type()) {
			case Question :: TYPE_PERCENTAGE:
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
				return $user_question->get_weight();
			default:
				$score = 0;
				$question = $rdm->retrieve_learning_object($user_question->get_question_id());
				$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
				$clo_answers = $rdm->retrieve_complex_learning_object_items($condition);
				while ($clo_answer = $clo_answers->next_result())
				{
					$score += $clo_answer->get_score();
				}	
				return $score;
		}
	}
}
?>