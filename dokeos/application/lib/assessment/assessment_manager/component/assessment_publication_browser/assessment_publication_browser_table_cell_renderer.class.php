<?php
/**
 * @package assessment.tables.assessment_publication_table
 */
require_once dirname(__FILE__).'/assessment_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/assessment_publication_table/default_assessment_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../assessment_publication.class.php';
require_once dirname(__FILE__).'/../../assessment_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author 
 */

class AssessmentPublicationBrowserTableCellRenderer extends DefaultAssessmentPublicationTableCellRenderer
{
	/**
	 * The browser component
	 * @var AssessmentManagerAssessmentPublicationsBrowserComponent
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function AssessmentPublicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $assessment_publication)
	{
		if ($column === AssessmentPublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($assessment_publication);
		}

		return parent :: render_cell($column, $assessment_publication);
	}

	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($assessment_publication)
	{
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->browser->get_assessment_publication_viewer_url($assessment_publication),
			'label' => Translation :: get('TakeAssessment'),
			'img' => Theme :: get_common_image_path().'action_next.png',
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_assessment_results_viewer_url($assessment_publication),
			'label' => Translation :: get('ViewResults'),
			'img' => Theme :: get_common_image_path().'action_view_results.png',
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_delete_assessment_publication_url($assessment_publication),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_update_assessment_publication_url($assessment_publication),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
		);

		$toolbar_data[] = array(
			'href' => $this->browser->get_assessment_publication_viewer_url($assessment_publication),
			'label' => Translation :: get('Hide'),
			'img' => Theme :: get_common_image_path().'action_visible.png',
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_assessment_publication_viewer_url($assessment_publication),
			'label' => Translation :: get('Export'),
			'img' => Theme :: get_common_image_path().'action_export.png',
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_assessment_publication_viewer_url($assessment_publication),
			'label' => Translation :: get('Move'),
			'img' => Theme :: get_common_image_path().'action_move.png',
		);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>