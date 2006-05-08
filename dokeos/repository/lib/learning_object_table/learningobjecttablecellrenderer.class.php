<?php
/**
 * A cell renderer which can be used for rendering a table of learning objects.
 * @package repository.learningobjecttable
 */
interface LearningObjectTableCellRenderer
{
	/**
	 * Render a table cell
	 * @param LearningObjectTableColumn $column The column in which this cell
	 * will be displayed
	 * @param LearningObject $learning_object The learning object which will be
	 * displayed in this cell
	 * @return string The rendered cell content
	 */
	function render_cell($column, $learning_object);
}
?>