<?php
require_once dirname(__FILE__).'/forumtabledataprovider.class.php';
require_once dirname(__FILE__).'/forumtablecolumnmodel.class.php';
require_once dirname(__FILE__).'/forumtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttable.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumTable extends LearningObjectTable
{
	function ForumTable($forum, $url_format)
	{
		$name = 'forumtable'.$forum->get_id();
		$data_provider = new ForumTableDataProvider($forum);
		$column_model = new ForumTableColumnModel();
		$cell_renderer = new ForumTableCellRenderer($url_format);
		parent :: __construct($data_provider, $name, $column_model, $cell_renderer);
	}
}
?>