<?php
/**
 * A column in a table of learning objects
 * @package repository
 */
class LearningObjectTableColumn
{
	/**
	 * The property of the learning object which will be displayed in this
	 * column
	 */
	private $learning_object_property;
	/**
	 * The title of this column
	 */
	private $title;
	/**
	 * Is sorting on this column allowed?
	 */
	private $sortable;
	/**
	 * Create a new column
	 * @param string $property_name_or_column_title If this column will display
	 * the value of a learning object property, this parameter should contain
	 * the name of this property. Else this parameter should contain the title
	 * of the column.
	 * @param boolean $contains_learning_object_property True if the parameter
	 * $property_name_or_column contains a property, false otherwise.
	 */
	function LearningObjectTableColumn($property_name_or_column_title, $contains_learning_object_property = false)
	{
		if ($contains_learning_object_property)
		{
			$this->learning_object_property = $property_name_or_column_title;
			$this->title = get_lang(ucfirst($this->learning_object_property));
			$this->sortable = true;
		}
		else
		{
			$this->title = $property_name_or_column_title;
			$this->sortable = false;
		}
	}
	/**
	 * Retrieve learning object property
	 * @return string The property name
	 */
	function get_learning_object_property()
	{
		return $this->learning_object_property;
	}
	/**
	 * Retrieve the title of this column
	 * @return string The title
	 */
	function get_title()
	{
		return $this->title;
	}
	/**
	 * Determine if the table can be sorted on this column
	 * @return boolean True if table can be sorted on this column
	 */
	function is_sortable()
	{
		return $this->sortable;
	}
	/**
	 * Set this columns title
	 * @param string $title The new title
	 */
	function set_title($title)
	{
		$this->title = $title;
	}
	/**
	 * Set if the table containing this column may be sorted on this column
	 * @param boolean $sortable True if table can be sorted on this column
	 */
	function set_sortable($sortable)
	{
		$this->sortable = $sortable;
	}
}
?>