<?php
/**
 * @package repository.publicationtable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../laika_attempt.class.php';
/**
 * TODO: Add comment
 */
class DefaultLaikaAttemptTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultLaikaAttemptTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $laika_attempt)
	{
		switch ($column->get_name())
		{
			case LaikaAttempt :: PROPERTY_DATE :
				return date('Y-m-d, H:i', $laika_attempt->get_date());
			default :
			    return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>