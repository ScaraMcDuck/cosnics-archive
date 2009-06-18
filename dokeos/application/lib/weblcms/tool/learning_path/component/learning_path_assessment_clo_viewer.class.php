<?php

require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

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
	
	function start_assessment()
	{
		
	}
	
	function save_answer($complex_question_id, $answer, $score)
	{
		$parameters = array();
		$parameters['lpi_attempt_id'] = $this->lpi_attempt_id;
		$parameters['lpi_attempt_id'] = $complex_question_id;
		$parameters['answer'] = $answer;
		$parameters['score'] = $score;
		$parameters['feedback'] = '';
		
		Events :: trigger_event('learning_path_question_attempts', 'weblcms', $parameters);
	}
	
	function finish_assessment($total_score)
	{
		$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $this->lpi_attempt_id);

		$dummy = new WeblcmsLpiAttemptTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		$lpi_tracker = $trackers[0];
		
		$lpi_tracker->set_score($total_score);
		$lpi_tracker->set_total_time($lpi_tracker->get_total_time() + (time() - $lpi_tracker->get_start_time()));
		$lpi_tracker->update();
	}

}
?>