<?php
/**
 * $Id: grouptool.class.php 12541 2007-06-06 07:34:34Z bmol $
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
class GroupTableColumn
{
	/**
	 * The property of the group which will be displayed in this column.
	 */
	private $group_property;
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
	 * of groups, or arbitrary content.
	 * @param string $property_name_or_column_title If the column contains arbitrary content, the title of the column. If
	 *   it displays a user property, that particular property, a User::PROPERTY_* constant.
	 * @param boolean $contains_user_property True if the column displays a user property, false otherwise.
	 */
	function GroupTableColumn($property_name_or_column_title, $contains_group_property = false)
	{
		if ($contains_group_property)
		{
			$this->group_property = $property_name_or_column_title;
			$this->title = get_lang(ucfirst($this->group_property));
			$this->sortable = true;
		}
		else
		{
			$this->title = $property_name_or_column_title;
			$this->sortable = false;
		}
	}
	/**
	 * Gets the group property that this column displays.
	 * @return string The property name, or null if the column contains
	 *                arbitrary content.
	 */
	function get_group_property()
	{
		return $this->group_property;
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