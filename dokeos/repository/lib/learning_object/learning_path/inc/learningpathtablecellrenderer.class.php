<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
require_once dirname(__FILE__).'/../../../learning_object_table/defaultlearningobjecttablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../learning_object.class.php';

class LearningPathTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	private $url_fmt;

	function LearningPathTableCellRenderer($url_fmt)
	{
		parent :: __construct();
		$this->url_fmt = $url_fmt;
	}

	function render_cell($column, $learning_object)
	{
		if ($column->get_learning_object_property() == LearningObject :: PROPERTY_TITLE)
		{
			return '<a href="'.htmlentities(sprintf($this->url_fmt, $learning_object->get_id())).'">'.parent :: render_cell($column, $learning_object).'</a>';
		}
		return parent :: render_cell($column, $learning_object);
	}
}
?>