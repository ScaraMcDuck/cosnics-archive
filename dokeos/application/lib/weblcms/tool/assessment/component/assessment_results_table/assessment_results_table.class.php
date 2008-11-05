<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_results_table
 */
require_once dirname(__FILE__).'/assessment_results_table_data_provider.class.php';
require_once dirname(__FILE__).'/assessment_results_table_column_model.class.php';
require_once dirname(__FILE__).'/assessment_results_table_cell_renderer.class.php';
//require_once dirname(__FILE__).'/../../../../learning_object_results_table.class.php';
require_once Path::get_library_path() . 'html/table/object_table/object_table.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * results.
 */
class AssessmentResultsTable extends ObjectTable
{
	const DEFAULT_NAME = 'assessment_results_table';
	
	/**
	 * Constructor.
	 * @param int $owner The id of the current user.
	 * @param array $types The types of objects that can be published in current
	 * location.
	 * @param string $query The search query, or null if none.
	 * @param string $publish_url_format URL for publishing the selected
	 * learning object.
	 * @param string $edit_and_publish_url_format URL for editing and publishing
	 * the selected learning object.
	 * @see ResultsCandidateTableCellRenderer::ResultsCandidateTableCellRenderer()
	 */
	function AssessmentResultsTable($parent, $owner, $pid = null)
	{
		$data_provider = new AssessmentResultsTableDataProvider($parent, $owner, $pid);
		$column_model = new AssessmentResultsTableColumnModel();
		$cell_renderer = new AssessmentResultsTableCellRenderer($parent);
		parent :: __construct($data_provider, AssessmentResultsTable :: DEFAULT_NAME, $column_model, $cell_renderer);
	}
	
	/**
	 * You should not be concerned with this method. It is only public because
	 * of technical limitations.
	 */
	function get_objects($offset, $count, $order_column, $order_direction)
	{
		$objects = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_column($order_column - ($this->has_form_actions() ? 1 : 0))->get_object_property(), $order_direction);
		$table_data = array ();
		$column_count = $this->get_column_model()->get_column_count();
		foreach ($objects as $object)
		{
			$row = array ();
			if ($this->has_form_actions())
			{
				$row[] = $object->get_id();
			}
			for ($i = 0; $i < $column_count; $i ++)
			{
				$row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $object);
			}
			$table_data[] = $row;
		}
		return $table_data;
	}
}
?>