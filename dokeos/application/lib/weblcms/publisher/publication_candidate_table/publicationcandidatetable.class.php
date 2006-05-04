<?php
require_once dirname(__FILE__).'/publicationcandidatetabledataprovider.class.php';
require_once dirname(__FILE__).'/publicationcandidatetablecolumnmodel.class.php';
require_once dirname(__FILE__).'/publicationcandidatetablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learning_object_table/learningobjecttable.class.php';

class PublicationCandidateTable extends LearningObjectTable
{
	function PublicationCandidateTable($owner, $types, $publish_url_format, $edit_and_publish_url_format)
	{
		$name = 'pubcand';
		$data_provider = new PublicationCandidateTableDataProvider($owner, $types);
		$column_model = new PublicationCandidateTableColumnModel();
		$cell_renderer = new PublicationCandidateTableCellRenderer($publish_url_format, $edit_and_publish_url_format);
		parent :: __construct($data_provider, $name, $column_model, $cell_renderer);
	}
}
?>