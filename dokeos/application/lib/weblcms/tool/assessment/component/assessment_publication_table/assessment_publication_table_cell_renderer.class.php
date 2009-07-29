<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_publication_table
 */
require_once Path :: get_repository_path(). 'lib/learning_object_table/default_learning_object_table_cell_renderer.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object/survey/survey.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/assessment_publication_table_column_model.class.php';
require_once Path :: get_application_path().'lib/weblcms/trackers/weblcms_assessment_attempts_tracker.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class AssessmentPublicationTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	private $table_actions;
	private $browser;
	/**
	 * Constructor.
	 * @param string $publish_url_format URL for publishing the selected
	 * learning object.
	 * @param string $edit_and_publish_url_format URL for editing and publishing
	 * the selected learning object.
	 */
	function AssessmentPublicationTableCellRenderer($browser)
	{
		$this->table_actions = array();
		$this->browser = $browser;
	}
	/*
	 * Inherited
	 */
	function render_cell($column, $publication)
	{
		if ($column === AssessmentPublicationTableColumnModel :: get_action_column())
		{
			return $this->get_actions($publication);
		} 
		else if ($column->get_name() == Assessment :: PROPERTY_ASSESSMENT_TYPE)
		{
			if ($publication->is_hidden())
			{
				return '<span style="color: gray">'. $publication->get_learning_object()->get_assessment_type().'</span>';
			}
			else
			{
				return $publication->get_learning_object()->get_assessment_type();
			}
		}
		if ($publication->is_hidden())
		{
			return '<span style="color: gray">'. parent :: render_cell($column, $publication->get_learning_object()).'</span>';
		}
		else
		{
			return parent :: render_cell($column, $publication->get_learning_object());
		}
	}
	
	function get_actions($publication) 
	{
		$assessment = $publication->get_learning_object();
		//$times_taken = WeblcmsDataManager :: get_instance()->times_taken($this->browser->get_user_id(), $assessment->get_id());
		$track = new WeblcmsAssessmentAttemptsTracker();
		$condition_t = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication->get_id());
		$condition_u = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->browser->get_user_id());
		$condition = new AndCondition(array($condition_t, $condition_u));
		$trackers = $track->retrieve_tracker_items($condition);
		
		$count = count($trackers);
		
		foreach($trackers as $tracker)
		{
			if($tracker->get_status() == 'not attempted')
			{
				$this->active_tracker = $tracker;
				$count--;
				break;
			}
		}

		if ($assessment->get_maximum_attempts() == 0 || $count < $assessment->get_maximum_attempts())
		{
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())),
			'label' => Translation :: get('TakeAssessment'),
			'img' => Theme :: get_common_image_path().'action_right.png'
			);
		}
		else 
		{
			$actions[] = array(
			//'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())),
			'label' => Translation :: get('TakeAssessment'),
			'img' => Theme :: get_common_image_path().'action_right_na.png'
			);
		}
			
		if ($this->browser->is_allowed(EDIT_RIGHT)) 
		{
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())), 
			'label' => Translation :: get('ViewResults'), 
			'img' => Theme :: get_common_image_path().'action_view_results.png'
			);
			
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_DELETE_PUBLICATION, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Delete'), 
			'img' => Theme :: get_common_image_path().'action_delete.png'
			);
			
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Edit'), 
			'img' => Theme :: get_common_image_path().'action_edit.png'
			);
			
			$img = 'action_visible.png';
			if ($publication->is_hidden())
			{
				$img = 'action_visible_na.png';
			}
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Visible'), 
			'img' => Theme :: get_common_image_path().$img
			);	
			
			$actions[] = array(
			'href' => $this->browser->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_QTI, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Export'), 
			'img' => Theme :: get_common_image_path().'action_export.png'
			);
			
			$actions[] = array(
			'href' => $this->browser->get_url(array(AssessmentTool :: PARAM_ACTION => Tool :: ACTION_MOVE_TO_CATEGORY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Move'), 
			'img' => Theme :: get_common_image_path().'action_move.png'
			);
			
			if ($assessment->get_assessment_type() == Survey :: TYPE_SURVEY)
			{
				$actions[] = array(
				'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, AssessmentTool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
				'label' => Translation :: get('InviteUsers'), 
				'img' => Theme :: get_common_image_path().'action_invite_users.png'
				);
			}
		} 
		else
		{

			/*$conditionuser = new EqualityCondition(UserAssessment :: PROPERTY_USER_ID, $this->browser->get_user_id());
			$conditionass = new EqualityCondition(UserAssessment :: PROPERTY_ASSESSMENT_ID, $publication->get_learning_object()->get_id());
			$user_assessments = WeblcmsDataManager :: get_instance()->retrieve_user_assessments(new AndCondition(array($conditionuser, $conditionass)));
			$user_assessment = $user_assessments->next_result();
			
			$conditions[] = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->browser->get_user_id());
			$conditions[] = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication->get_learning_object()->get_id());
			$condition = new AndCondition($conditions);
			$dummy = new WeblcmsAssessmentAttemptsTracker();
			$user_assessments = $dummy->retrieve_tracker_items($condition);
			$user_assessment = $user_assessments[0];
			
			if (count($user_assessments) > 0) 
			{
				$actions[] = array(
					'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id())), 
					'label' => Translation :: get('ViewResults'), 
					'img' => Theme :: get_common_image_path().'action_view_results.png'
				);
			}
			else 
			{
				$actions[] = array(
					//'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id())), 
					'label' => Translation :: get('ViewResultsNA'), 
					'img' => Theme :: get_common_image_path().'action_view_results_na.png'
				);
			}*/
			
			$actions[] = array(
				'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())), 
				'label' => Translation :: get('ViewResults'), 
				'img' => Theme :: get_common_image_path().'action_view_results.png'
			);
		}
		
		return DokeosUtilities :: build_toolbar($actions);
	}
	
	/**
	 * Gets the links to publish or edit and publish a learning object.
	 * @param LearningObject $learning_object The learning object for which the
	 * links should be returned.
	 * @return string A HTML-representation of the links.
	 */
	private function get_publish_links($learning_object)
	{
		$toolbar_data = array();
		$table_actions = $this->table_actions;
		
		foreach($table_actions as $table_action)
		{
			$table_action['href'] = sprintf($table_action['href'], $learning_object->get_id());
			$toolbar_data[] = $table_action;
		}
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>