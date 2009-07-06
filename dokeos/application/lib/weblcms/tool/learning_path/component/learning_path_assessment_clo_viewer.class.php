<?php

require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_lpi_attempt_tracker.class.php';

class LearningPathToolAssessmentCloViewerComponent extends LearningPathToolComponent
{
	private $lpi_attempt_id;
	
	function run()
	{
        $assessment = Request :: get('oid'); 
        $lpi_attempt_id = Request :: get('lpi_attempt_id');
        
       	$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($assessment);
        
        $this->lpi_attempt_id = $lpi_attempt_id;
        
        $this->set_parameter(LearningPathTool :: PARAM_ACTION, LearningPathTool :: ACTION_VIEW_ASSESSMENT_CLO);
        $this->set_parameter('oid', $assessment);
        $this->set_parameter('lpi_attempt_id', $lpi_attempt_id);
        
		$display = ComplexDisplay :: factory($this, $object->get_type());
        $display->set_root_lo($object);
        
		Display :: small_header();
		$display->run();
	}
	
	function change_answer_data($complex_question_id, $score, $feedback)
	{
		if(!$score && !$feedback) return;
		
		$conditions = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_LPI_ATTEMPT_ID, $this->lpi_attempt_id);
		$conditions = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $complex_question_id);
		$condition = new AndCondition($conditions);

		$dummy = new WeblcmsLearningPathQuestionAttemptsTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		$lpi_tracker = $trackers[0];
		
		if($score)
			$lpi_tracker->set_score($score);
		
		if($feedback)
			$lpi_tracker->set_feedback($feedback);
			
		$lpi_tracker->update();
	}
	
	function save_answer($complex_question_id, $answer, $score)
	{
		$parameters = array();
		$parameters['lpi_attempt_id'] = $this->lpi_attempt_id;
		$parameters['question_cid'] = $complex_question_id;
		$parameters['answer'] = $answer;
		$parameters['score'] = $score;
		$parameters['feedback'] = '';
		
		Events :: trigger_event('attempt_learning_path_question', 'weblcms', $parameters);
	}
	
	function finish_assessment($total_score)
	{
		$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $this->lpi_attempt_id);

		$dummy = new WeblcmsLpiAttemptTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		$lpi_tracker = $trackers[0];
		
		$lpi_tracker->set_score($total_score);
		$lpi_tracker->set_total_time($lpi_tracker->get_total_time() + (time() - $lpi_tracker->get_start_time()));
		$lpi_tracker->set_status('completed');
		$lpi_tracker->update();
	}

}
?>