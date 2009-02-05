<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_publication_table
 */
require_once Path :: get_repository_path(). 'lib/learning_object_table/default_learning_object_table_cell_renderer.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/assessment_results_table_detail_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class AssessmentResultsTableDetailCellRenderer extends DefaultLearningObjectTableCellRenderer
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
	function AssessmentResultsTableDetailCellRenderer($browser)
	{
		$this->table_actions = array();
		$this->browser = $browser;
	}
	/*
	 * Inherited
	 */
	function render_cell($column, $user_assessment)
	{
		
		if ($column === AssessmentResultsTableDetailColumnModel :: get_action_column())
		{
			return $this->get_actions($user_assessment);
		} 
		else
		{
			switch ($column->get_object_property())
			{
				case WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID:
					$user_id = $user_assessment->get_user_id();
					if ($user_id > 0)
						return UserDataManager :: get_instance()->retrieve_user($user_id)->get_fullname();
					else
						return 'Anonymous';
				case WeblcmsAssessmentAttemptsTracker :: PROPERTY_TOTAL_SCORE:
					$total = $user_assessment->get_total_score();
					$pub = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($user_assessment->get_assessment_id());
					$assessment = $pub->get_learning_object();
					$max = $assessment->get_maximum_score();
					$pct = round(($total / $max) * 100, 2);
					return $total.'/'.$max.' ('.$pct.'%)';
				case WeblcmsAssessmentAttemptsTracker :: PROPERTY_DATE:
					return $user_assessment->get_date();
				default:
					return '';
			}
		}
	}
	
	function get_actions($user_assessment) 
	{
		$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id())),
			'label' => Translation :: get('ViewResults'),
			'img' => Theme :: get_common_image_path().'action_view_results.png'
		);
		
		$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id())),
			'label' => Translation :: get('ExportResults'),
			'img' => Theme :: get_common_image_path().'action_export.png'
		);
		
		$pub = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($user_assessment->get_assessment_id());
		$assessment = $pub->get_learning_object();
		if ($assessment->get_assessment_type() == Assessment :: TYPE_ASSIGNMENT)
		{
			$actions[] = array(
				'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_SAVE_DOCUMENTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $user_assessment->get_id())),
				'label' => Translation :: get('DownloadDocuments'),
				'img' => Theme :: get_common_image_path().'action_download.png'
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