<?php
require_once dirname(__FILE__).'/exercise_results_viewer.class.php';
require_once dirname(__FILE__).'/survey_results_viewer.class.php';
require_once dirname(__FILE__).'/assignment_results_viewer.class.php';


abstract class ResultsViewer extends FormValidator
{
	private $user_assessment;
	private $edit_rights;
	
	function ResultsViewer($user_assessment, $edit_rights, $url, $component)
	{
		parent :: __construct('assessment', 'post', $url);
		$this->user_assessment = $user_assessment;
		$this->edit_rights = $edit_rights;
	}
	
	abstract function build();
	
	function get_user_assessment()
	{
		return $this->user_assessment;
	}
	
	function get_edit_rights()
	{
		return $this->edit_rights;
	}
	
	function get_assessment() 
	{
		return $this->get_publication()->get_learning_object();
	}
	
	function get_publication()
	{
		$webdm = WeblcmsDataManager :: get_instance();
		$pub = $webdm->retrieve_learning_object_publication($this->user_assessment->get_assessment_id());
		return $pub;
	}
	
	static function factory($user_assessment, $edit_rights, $url,$component)
	{
		$pub = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($user_assessment->get_assessment_id());
		$assessment = $pub->get_learning_object();
		
		switch ($assessment->get_assessment_type()) 
		{
			case Assessment :: TYPE_ASSIGNMENT:
				$subcomponent = new AssignmentResultsViewer($user_assessment, $edit_rights, $url,$component);
				break;
			case Assessment :: TYPE_EXERCISE:
				$subcomponent = new ExerciseResultsViewer($user_assessment, $edit_rights, $url,$component);
				break;
			case Survey :: TYPE_SURVEY:
				if ($edit_rights)
					$subcomponent = new ExerciseResultsViewer($user_assessment, $edit_rights, $url,$component);
				else
					$subcomponent = new SurveyResultsViewer($user_assessment, $edit_rights, $url,$component);
				break;
			default:
				$subcomponent = new ExerciseResultsViewer($user_assessment, $edit_rights, $url,$component);
				break;
		}
		return $subcomponent;
	}
		
}
?>