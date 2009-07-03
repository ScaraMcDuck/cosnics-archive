<?php
/**
 * $Id: assessment_tool.class.php 16640 2008-10-29 11:12:07Z Scara84 $
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage assessment
 */

require_once dirname(__FILE__) . '/../complex_display.class.php';
require_once dirname(__FILE__) . '/assessment_display_component.class.php';
/**
 * This tool allows a user to publish assessments in his or her course.
 */
class AssessmentDisplay extends ComplexDisplay
{
	const ACTION_VIEW_ASSESSMENT = 'view';

	/**
	 * Inherited.
	 */
	function run()
	{
		$component = parent :: run();
	
		if(!$component)
		{
			$action = $this->get_action();
			
			switch ($action)
			{
				case self :: ACTION_VIEW_ASSESSMENT :
					$component = AssessmentDisplayComponent :: factory('AssessmentViewer', $this);
					break;
				default :
					$component = AssessmentDisplayComponent :: factory('AssessmentViewer', $this);
			}
		}
		
		$component->run();
	}
	
	function save_answer($complex_question_id, $answer, $score)
	{
		return $this->get_parent()->save_answer($complex_question_id, $answer, $score);
	}
	
	function finish_assessment($total_score)
	{
		return $this->get_parent()->finish_assessment($total_score);
	}
}
?>