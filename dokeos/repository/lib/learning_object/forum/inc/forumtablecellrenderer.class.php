<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../../content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../content_object.class.php';

class ForumTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
	private $url_fmt;

	function ForumTableCellRenderer($url_fmt)
	{
		parent :: __construct();
		$this->url_fmt = $url_fmt;
	}

	function render_cell($column, $content_object)
	{
		if ($column->get_content_object_property() == ContentObject :: PROPERTY_TITLE)
		{
			return '<a href="'.htmlentities(sprintf($this->url_fmt, $content_object->get_id())).'">'.parent :: render_cell($column, $content_object).'</a>';
		}
		return parent :: render_cell($column, $content_object);
	}
}
?>