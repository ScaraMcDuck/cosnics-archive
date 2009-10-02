<?php

require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class AssessmentDisplay extends LearningPathContentObjectDisplay
{
	function display_content_object($assessment, $tracker_attempt_data)
	{
		$lpi_attempt_id = $tracker_attempt_data['active_tracker']->get_id();
		
		$link = $this->get_parent()->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_ASSESSMENT_CLO, 
											 'oid' => $assessment->get_id(), 
											 'lpi_attempt_id' => $lpi_attempt_id));
		$html[] = $this->display_link($link);
		
		return implode("\n", $html);
	}
}
?>