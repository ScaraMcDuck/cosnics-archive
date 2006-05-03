<?php
require_once dirname(__FILE__).'/learningpathtabledataprovider.class.php';
require_once dirname(__FILE__).'/learningpathtablecolumnmodel.class.php';
require_once dirname(__FILE__).'/learningpathtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/learningobjecttable.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathTable extends LearningObjectTable
{
	function LearningPathTable($learningpath, $url_format)
	{
		$name = 'learningpathtable'.$learningpath->get_id();
		$data_provider = new LearningPathTableDataProvider($learningpath);
		$column_model = new LearningPathTableColumnModel();
		$cell_renderer = new LearningPathTableCellRenderer($url_format);
		parent :: __construct($data_provider, $name, $column_model, $cell_renderer);
	}
}
?>