<?php
/**
 * @package application.lib.profiler.publisher.publication_candidate_table
 */
require_once Path :: get_repository_path(). 'lib/learning_object_table/default_learning_object_table_cell_renderer.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/publication_candidate_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class PublicationCandidateTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	private $table_actions;
	/**
	 * Constructor.
	 * @param string $publish_url_format URL for publishing the selected
	 * learning object.
	 * @param string $edit_and_publish_url_format URL for editing and publishing
	 * the selected learning object.
	 */
	function PublicationCandidateTableCellRenderer($table_actions)
	{
		$this->table_actions = $table_actions;
	}
	/*
	 * Inherited
	 */
	function render_cell($column, $learning_object)
	{
		if ($column === PublicationCandidateTableColumnModel :: get_action_column())
		{
			return $this->get_publish_links($learning_object);
		}
		return parent :: render_cell($column, $learning_object);
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