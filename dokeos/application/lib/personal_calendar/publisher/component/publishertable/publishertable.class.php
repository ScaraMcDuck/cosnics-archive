<?php
/**
 * $Id$
 * @package application.personal_calendar
 * @subpackage publisher
 */
require_once dirname(__FILE__).'/../../../../../../repository/lib/learning_object_table/defaultlearningobjecttablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../../../../repository/lib/learningobject.class.php';
require_once dirname(__FILE__).'/../../../../../../repository/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/publishertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/publishertabledataprovider.class.php';
require_once dirname(__FILE__).'/publishertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../../../../repository/lib/learning_object_table/learningobjecttable.class.php';

/**
 * This class is a cell renderer for a publication candidate table
 */
class PublisherTable extends LearningObjectTable
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
	function PublisherTable($owner, $types, $query, $publish_url_format, $edit_and_publish_url_format)
	{
		$name = 'pubcand';
		$data_provider = new PublisherTableDataProvider($owner, $types, $query);
		$column_model = new PublisherTableColumnModel();
		$cell_renderer = new PublisherTableCellRenderer($publish_url_format, $edit_and_publish_url_format);
		parent :: __construct($data_provider, $name, $column_model, $cell_renderer);
	}
}
?>