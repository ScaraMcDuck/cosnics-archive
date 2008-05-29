<?php
/**
 * @package application.lib.profiler.publisher.publication_candidate_table
 */
require_once dirname(__FILE__).'/publication_candidate_table_data_provider.class.php';
require_once dirname(__FILE__).'/publication_candidate_table_column_model.class.php';
require_once dirname(__FILE__).'/publication_candidate_table_cell_renderer.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * publication.
 */
class PublicationCandidateTable extends ObjectTable
{
	const DEFAULT_NAME = 'publication_candidate_table';
	
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
	 * @see PublicationCandidateTableCellRenderer::PublicationCandidateTableCellRenderer()
	 */
	function PublicationCandidateTable($owner, $types, $query, $table_actions)
	{
		$data_provider = new PublicationCandidateTableDataProvider($owner, $types, $query);
		$column_model = new PublicationCandidateTableColumnModel();
		$cell_renderer = new PublicationCandidateTableCellRenderer($table_actions);
		parent :: __construct($data_provider, PublicationCandidateTable :: DEFAULT_NAME, $column_model, $cell_renderer);
	}
}
?>