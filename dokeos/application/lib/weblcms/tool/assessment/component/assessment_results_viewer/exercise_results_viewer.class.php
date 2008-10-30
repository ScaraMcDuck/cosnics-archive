<?php
require_once dirname(__FILE__).'/results_viewer.class.php';

class ExerciseResultsViewer extends ResultsViewer
{
	
	function to_html()
	{
		return 'Exercise results viewer';
		$exercise = parent :: get_assessment();
		$assessment_id = $assessment->get_id();
		
		$uaid = parent :: get_user_assessment()->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$db = WeblcmsDataManager :: get_instance();
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment_id);
		$clo_questions = $dm->retrieve_complex_learning_object_items($condition);
		
		while($clo_question = $clo_questions->next_result())
		{
			$question = $dm->retrieve_learning_object($clo_question->get_ref(), 'question');
		}
		
		
	}
}
?>