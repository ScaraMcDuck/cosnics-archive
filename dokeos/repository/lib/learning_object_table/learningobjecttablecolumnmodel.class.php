<?php
/**
 * This table column model holds the different columns of a table of learning
 * objects.
 * @package repository
 */
class LearningObjectTableColumnModel
{
	/**
	 * The columns in the table
	 */
	private $columns;
	/**
	 * The column on which the table is currently sorted
	 */
	private $order_column;
	/**
	 * The order direction in which the table is currently sorted
	 */
	private $order_direction;
	/**
	 * Create a new table column model.
	 * @param array $columns An array of columns
	 * @param int $default_order_column The index of the column on which the
	 * table should be sorted by default
	 * @param int $default_order_direction Possible values are SORT_ASC and
	 * SORT_DESC
	 */
	function LearningObjectTableColumnModel($columns, $default_order_column = 0, $default_order_direction = SORT_ASC)
	{
		$this->columns = $columns;
		$this->order_column = $default_order_column;
		$this->order_direction = $default_order_direction;
	}
	/**
	 * retrieve the number of columns
	 * @return int
	 */
	function get_column_count()
	{
		return count($this->columns);
	}
	/**
	 * Get a column
	 * @param int $index The number of the column which should be returned
	 * @return LearningObjectTableColumn The requested column
	 */
	function get_column($index)
	{
		return $this->columns[$index];
	}
	/**
	 * Add a column to the end of the column-list
	 * @param LearningObjectTableColumn The new column
	 */
	function add_column($column)
	{
		$this->columns[] = $column;
	}
	/**
	 * Get the default order column
	 * @return LearningObjectTableColumn The default order column
	 */
	function get_default_order_column()
	{
		return $this->order_column;
	}
	/**
	 * Set the default order column
	 * @param int $column_index The index of the column on which the table
	 * should be sorted by default
	 */
	function set_default_order_column($column_index)
	{
		$this->order_column = $column_index;
	}
	/**
	 * Get the default order direction
	 * @return int The default order direction
	 */
	function get_default_order_direction()
	{
		return $this->order_direction;
	}
}
?>