<?php
require_once Path :: get_library_path() . 'filecompression/filecompression.class.php';

class AssessmentToolDocumentSaverComponent extends AssessmentToolComponent
{
	function run()
	{
		if (isset($_GET[AssessmentTool :: PARAM_ASSESSMENT]))
		{
			$id = $_GET[AssessmentTool :: PARAM_ASSESSMENT];
			$filenames = $this->save_assessment_docs($id);
		}
		else if (isset($_GET[AssessmentTool :: PARAM_USER_ASSESSMENT]))
		{
			$id = $_GET[AssessmentTool :: PARAM_USER_ASSESSMENT];
			$filenames = $this->save_user_assessment_docs($id);
		}
		$this->send_files($filenames, $id);
	}
	
	function save_assessment_docs($assessment_id)
	{
		$condition = new EqualityCondition(UserAssessment :: PROPERTY_ASSESSMENT_ID, $assessment_id);
		$user_assessments = WeblcmsDataManager :: get_instance()->retrieve_user_assessments($condition);
		
		while ($user_assessment = $user_assessments->next_result())
		{
			$ua_filenames = $this->save_user_assessment_docs($user_assessment->get_id());
			foreach($ua_filenames as $file)
			{
				$filenames[] = $file;
			}
		}
		return $filenames;
	}
	
	function save_user_assessment_docs($user_assessment_id)
	{
		$user_assessment = WeblcmsDataManager :: get_instance()->retrieve_user_assessment($user_assessment_id);
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $user_assessment->get_assessment_id());
		$rdm = RepositoryDataManager :: get_instance();
		$clo_questions = $rdm->retrieve_complex_learning_object_items($condition);
		
		while ($clo_question = $clo_questions->next_result())
		{
			$question = $rdm->retrieve_learning_object($clo_question->get_ref(), 'question');
			if ($question->get_question_type() == Question :: TYPE_DOCUMENT)
			{
				$questions[] = $question;
			}
		}
		
		foreach ($questions as $question)
		{
			$conditiona = new EqualityCondition(UserQuestion :: PROPERTY_USER_ASSESSMENT_ID, $user_assessment_id);
			$conditionq = new EqualityCondition(UserQuestion :: PROPERTY_QUESTION_ID, $question->get_id());
			$condition = new AndCondition(array($conditiona, $conditionq));
			$user_question = WeblcmsDataManager :: get_instance()->retrieve_user_questions($condition)->next_result();
			$condition = new EqualityCondition(UserAnswer :: PROPERTY_USER_QUESTION_ID, $user_question->get_id());
			$user_answer = WeblcmsDataManager :: get_instance()->retrieve_user_answers($condition)->next_result();
			if ($user_answer->get_extra() != 0)
			{
				$document = $rdm->retrieve_learning_object($user_answer->get_extra(), 'document');
				$filenames[] = Path :: get(SYS_REPO_PATH).$document->get_path();
			}
		}
		
		return $filenames;
	}
	
	function send_files($filenames, $assessment_id)
	{
		$temp_dir = Path :: get(SYS_TEMP_PATH) . 'retrieve_docs/'.$assessment_id.'/';
  		if(!is_dir($temp_dir))
  		{
  			mkdir($temp_dir, '0777', true);
  		}
  		
		foreach($filenames as $filename)
		{
			$newfile = $temp_dir . basename($filename);
			Filesystem :: copy_file($filename, $newfile);
		}
		
		$zip = Filecompression :: factory();
		$path = $zip->create_archive($temp_dir);
		//echo $path;
		FileSystem::remove($temp_dir);
		
		header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
		header('Cache-Control: public');
		header('Pragma: no-cache');
		header('Content-type: application/octet-stream');
		header('Content-length: '.filesize($path));
			
		if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT']))
		{
			header('Content-Disposition: filename= '.basename($path));
		}
		else
		{
			header('Content-Disposition: attachment; filename= '.basename($path));
		}
		
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
		{
			header('Pragma: ');
			header('Cache-Control: ');
			header('Cache-Control: public'); // IE cannot download from sessions without a cache
		}
		
		header('Content-Description: '.basename($path));
		header('Content-transfer-encoding: binary');
		$fp = fopen($path, 'r');
		fpassthru($fp);
		fclose($fp);
		Filesystem :: remove($path);
	}
}
?>