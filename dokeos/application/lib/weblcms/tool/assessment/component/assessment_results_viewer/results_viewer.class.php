<?php

abstract class ResultsViewer extends FormValidator
{
	private $user_assessment;
	private $edit_rights;
	
	function ResultsViewer($user_assessment, $edit_rights, $url)
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
		$repdm = RepositoryDataManager :: get_instance();
		$assessment = $repdm->retrieve_learning_object($this->user_assessment->get_assessment_id());
		return $assessment;
	}
	
	static function factory($user_assessment, $edit_rights, $url)
	{
		$repdm = RepositoryDataManager :: get_instance();
		$assessment = $repdm->retrieve_learning_object($user_assessment->get_assessment_id());
		
		switch ($assessment->get_assessment_type()) 
		{
			case Assessment::TYPE_ASSIGNMENT:
				$subcomponent = new AssignmentResultsViewer($user_assessment, $edit_rights, $url);
				break;
			case Assessment::TYPE_EXERCISE:
				$subcomponent = new ExerciseResultsViewer($user_assessment, $edit_rights, $url);
				break;
			case Survey::TYPE_SURVEY:
				$subcomponent = new SurveyResultsViewer($user_assessment, $edit_rights, $url);
				break;
			default:
				$subcomponent = new ExerciseResultsViewer($user_assessment, $edit_rights, $url);
				break;
		}
		return $subcomponent;
	}
		
}
?>