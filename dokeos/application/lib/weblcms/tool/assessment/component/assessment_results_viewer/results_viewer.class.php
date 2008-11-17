<?php

abstract class ResultsViewer extends FormValidator
{
	private $user_assessment;
	private $edit_rights;
	
	function ResultsViewer($user_assessment, $edit_rights)
	{
		$this->user_assessment = $user_assessment;
		$this->edit_rights = $edit_rights;
	}
	
	abstract function to_html();
	
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
		$assessment = $repdm->retrieve_learning_object($this->user_assessment->get_assessment_id(), 'assessment');
		return $assessment;
	}
		
}
?>