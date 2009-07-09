<?php
/**
 * @package common
 * @subpackage html
 * @subpackage table
 */
require_once Path :: get_library_path() . 'html/table/table_column.class.php';

class ObjectTableColumn implements TableColumn
{
    /**
     * The property of the object which will be displayed in this
     * column.
     */
    private $property;
    /**
     * The title of the column.
     */
    private $title;

    private $is_sortable;

    /**
     * Constructor. Either defines a column that displays a default property
     * of learning objects, or arbitrary content.
     * @param string $property If the column contains
     *                                              arbitrary content, the
     *                                              title of the column. If
     *                                              it displays a learning
     *                                              object property, that
     *                                              particular property, a
     *                                              LearningObject::PROPERTY_*
     *                                              constant.
     * @param boolean $contains_learning_object_property True if the column
     *                                                   displays a learning
     *                                                   object property, false
     *                                                   otherwise.
     */
    function ObjectTableColumn($property, $is_sortable = true)
    {
		$this->property = $property;
		$this->title = Translation :: get(DokeosUtilities :: underscores_to_camelcase($this->property));
		$this->is_sortable = $is_sortable;
    }

    /**
     * Gets the learning object property that this column displays.
     * @return string The property name, or null if the column contains
     *                arbitrary content.
     */
    function get_property()
    {
        return $this->property;
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
     * Determine if the table's contents may be sorted by this column.
     * @return boolean True if sorting by this column is allowed, false
     *                 otherwise.
     */
    function is_sortable()
    {
        return $this->is_sortable;
    }

    /**
     * Sets the title of this column.
     * @param string $title The new title.
     */
    function set_title($title)
    {
        $this->title = $title;
    }

    function get_name()
    {
        return $this->get_property();
    }
}
?>