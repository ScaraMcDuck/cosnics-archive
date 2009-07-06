<?php
/**
 * @package repository.usertable
 */
/**
 * 
 * TODO: Add comment
 * 
 */
class EventTableColumnModel
{
    /**
     * The columns in the table.
     */
    private $columns;
    /**
     * The column by which the table is currently sorted.
     */
    private $order_column;
    /**
     * The direction in which the table is currently sorted.
     */
    private $order_direction;

    /**
     * Constructor. Creates a new learning object table model.
     * @param array $columns The columns to use in the table. An array of
     *                       LearningObjectTableColumn instances.
     * @param int $default_order_column The column to order objects by by
     *                                  default, passed as the index of the
     *                                  column in $columns.
     * @param string $default_order_direction The default order direction.
     *                                        Either the PHP constant SORT_ASC
     *                                        or SORT_DESC.
     */
    function EventTableColumnModel($columns, $default_order_column = 0, $default_order_direction = SORT_ASC)
    {
        $this->columns = $columns;
        $this->order_column = $default_order_column;
        $this->order_direction = $default_order_direction;
    }

    /**
     * Gets the number of columns in the model.
     * @return int The column count.
     */
    function get_column_count()
    {
        return count($this->columns);
    }

    /**
     * Gets the column at the given index in the model.
     * @param int $index The index.
     * @return LearningObjectTableColumn The column.
     */
    function get_column($index)
    {
        return $this->columns[$index];
    }

    /**
     * Adds the given column at the end of the table.
     * @param LearningObjectTableColumn The column.
     */
    function add_column($column)
    {
        $this->columns[] = $column;
    }

    /**
     * Gets the index of the column to order learning objects by by default.
     * @return int The column index.
     */
    function get_default_order_column()
    {
        return $this->order_column;
    }

    /**
     * Sets the index of the column to order learning objects by by default.
     * @param int $column The index.
     */
    function set_default_order_column($column_index)
    {
        $this->order_column = $column_index;
    }

    /**
     * Gets the default order direction.
     * @return string The direction. Either the PHP constant SORT_ASC or
     *                SORT_DESC.
     */
    function get_default_order_direction()
    {
        return $this->order_direction;
    }

    /**
     * Sets the default order direction.
     * @param string $direction The direction. Either the PHP constant SORT_ASC
     *                          or SORT_DESC.
     */
    function set_default_order_direction($direction)
    {
        return $this->order_direction;
    }
}
?>