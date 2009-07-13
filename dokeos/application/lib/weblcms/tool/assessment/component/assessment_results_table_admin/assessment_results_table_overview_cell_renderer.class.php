<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_publication_table
 */
require_once Path :: get_repository_path(). 'lib/learning_object_table/default_learning_object_table_cell_renderer.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/assessment_results_table_overview_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class AssessmentResultsTableOverviewAdminCellRenderer extends DefaultLearningObjectTableCellRenderer
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
	function AssessmentResultsTableOverviewAdminCellRenderer($browser)
	{
		$this->table_actions = array();
		$this->browser = $browser;
	}
	/*
	 * Inherited
	 */
	function render_cell($column, $publication)
	{
		$assessment = $publication->get_learning_object();
		if ($column === AssessmentResultsTableOverviewAdminColumnModel :: get_action_column())
		{
			return $this->get_actions($publication);
		}
		else
		{
			switch ($column->get_name())
			{
				case Translation :: get(Assessment :: PROPERTY_TITLE):
					return $assessment->get_title();
				case Translation :: get(Assessment :: PROPERTY_ASSESSMENT_TYPE):
					return $assessment->get_assessment_type();
				case Translation :: get(Assessment :: PROPERTY_AVERAGE_SCORE):
					$track = new WeblcmsAssessmentAttemptsTracker();
					$avg = $track->get_average_score($publication);
					if (!isset($avg))
					{
						return 'No results';
					}
					else
					{
						return $avg . '%';
					}
				case Translation :: get(Assessment :: PROPERTY_TIMES_TAKEN):
					$track = new WeblcmsAssessmentAttemptsTracker();
					return $track->get_times_taken($publication);
				default:
					return '';
			}
		}
	}

	function get_actions($publication)
	{
		$assessment = $publication->get_learning_object();
		$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())),
			'label' => Translation :: get('ViewResults'),
			'img' => Theme :: get_common_image_path().'action_view_results.png'
		);

		$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_DELETE_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())),
			'label' => Translation :: get('DeleteAllResults'),
			'img' => Theme :: get_common_image_path().'action_delete.png'
		);

		if ($assessment->get_assessment_type() == Assessment :: TYPE_ASSIGNMENT)
		{
			$actions[] = array(
				'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_SAVE_DOCUMENTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())),
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