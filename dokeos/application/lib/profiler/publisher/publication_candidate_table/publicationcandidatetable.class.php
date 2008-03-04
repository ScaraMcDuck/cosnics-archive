<?php
/**
 * @package application.lib.profiler.publisher.publication_candidate_table
 */
require_once dirname(__FILE__).'/publicationcandidatetabledataprovider.class.php';
require_once dirname(__FILE__).'/publicationcandidatetablecolumnmodel.class.php';
require_once dirname(__FILE__).'/publicationcandidatetablecellrenderer.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_table/learningobjecttable.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * publication.
 */
class PublicationCandidateTable extends LearningObjectTable
{
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
	function PublicationCandidateTable($owner, $types, $query, $publish_url_format, $edit_and_publish_url_format)
	{
		$name = 'pubcand';
		$data_provider = new PublicationCandidateTableDataProvider($owner, $types, $query);
		$column_model = new PublicationCandidateTableColumnModel();
		$cell_renderer = new PublicationCandidateTableCellRenderer($publish_url_format, $edit_and_publish_url_format);
		parent :: __construct($data_provider, $name, $column_model, $cell_renderer);
	}
}
?>