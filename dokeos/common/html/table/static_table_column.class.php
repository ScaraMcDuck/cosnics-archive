<?php
/**
 * @package common
 * @subpackage html
 * @subpackage table
 */
require_once Path :: get_library_path() . 'html/table/table_column.class.php';

class StaticTableColumn implements TableColumn
{
    /**
     * The title of the column.
     */
    private $title;

    function StaticTableColumn($title)
    {
		$this->title = $title;
    }

    /**
     * Gets the title of this column.
     * @return string The title.
     */
    function get_title()
    {
        return $this->title;
    }

    /**
     * Sets the title of this column.
     * @param string $title The new title.
     */
    function set_title($title)
    {
        $this->title = $title;
    }

    /**
     * Determine if the table's contents may be sorted by this column.
     * @return boolean True if sorting by this column is allowed, false
     *                 otherwise.
     */
    function is_sortable()
    {
        return false;
    }

    function get_name()
    {
        return $this->get_title();
    }
}
?>