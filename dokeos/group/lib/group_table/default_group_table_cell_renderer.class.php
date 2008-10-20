<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../group.class.php';
/**
 * TODO: Add comment
 */
class DefaultGroupTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultGroupTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $group)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case Group :: PROPERTY_ID :
					return $group->get_id();
				case Group :: PROPERTY_NAME :
					return $group->get_name();
				case Group :: PROPERTY_DESCRIPTION :
					return $group->get_description();
			}
		}
		return '&nbsp;';
	}
}
?>