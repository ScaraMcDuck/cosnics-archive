<?php

abstract class ResultsViewer
{
	private $user_assessment;
	
	function ResultsViewer($user_assessment)
	{
		$this->user_assessment = $user_assessment;
	}
	
	abstract function to_html();
	
	function get_user_assessment()
	{
		return $user_assessment;
	}
	
	function get_assessment() 
	{
		$repdm = RepositoryDataManager :: get_instance();
		$assessment = $repdm->retrieve_learning_object($user_assessment->get_assessment_id(), 'assessment');
	}
		
}
?>