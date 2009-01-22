<?php
/**
 * @package common.html.table.common
 */
/**
 * 
 * TODO: Add comment
 * 
 */
class ObjectTableColumn
{
	/**
	 * The property of the object which will be displayed in this
	 * column.
	 */
	private $object_property;
	/**
	 * The title of the column.
	 */
	private $title;
	/**
	 * Whether or not sorting by this column is allowed.
	 */
	private $sortable;
	/**
	 * Constructor. Either defines a column that displays a default property
	 * of learning objects, or arbitrary content.
	 * @param string $property_name_or_column_title If the column contains
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
	function ObjectTableColumn($property_name_or_column_title, $contains_object_property = false)
	{
		if ($contains_object_property)
		{
			$this->object_property = $property_name_or_column_title;
			$this->title = Translation :: get(DokeosUtilities :: underscores_to_camelcase($this->object_property));
			$this->sortable = true;
		}
		else
		{
			$this->title = $property_name_or_column_title;
			$this->sortable = false;
		}
	}
	/**
	 * Gets the learning object property that this column displays.
	 * @return string The property name, or null if the column contains
	 *                arbitrary content.
	 */
	function get_object_property()
	{
		return $this->object_property;
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
		return $this->sortable;
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
	 * Sets whether or not the table's contents may be sorted by this column.
	 * @param boolean $sortable True if sorting by this column should be
	 *                          allowed, false otherwise.
	 */
	function set_sortable($sortable)
	{
		$this->sortable = $sortable;
	}
}
?>