<?php
/**
 * @package repository.learningobjecttable
 */
/**
 * Implementations of this interface are used to render the content of
 * individual cells in a learning object table.
 * 
 * @see LearningObjectTable
 * @author Tim De Pauw
 */
interface LearningObjectTableCellRenderer
{
	/**
	 * Renders a cell in a learning object table.
	 * @param LearningObjectTableColumn $column The column to which the cell
	 *                                          belongs.
	 * @param LearningObject $learning_object The learning object to which the
	 *                                        row that the cell is a part of
	 *                                        belongs.
	 * @return string The rendered cell contents. A valid HTML string.
	 */
	function render_cell($column, $learning_object);
}
?>