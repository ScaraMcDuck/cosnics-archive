<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../class_group.class.php';
/**
 * TODO: Add comment
 */
class DefaultClassGroupTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultClassGroupTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $classgroup)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case ClassGroup :: PROPERTY_ID :
					return $classgroup->get_id();
				case ClassGroup :: PROPERTY_NAME :
					return $classgroup->get_name();
				case ClassGroup :: PROPERTY_DESCRIPTION :
					return $classgroup->get_description();
			}
		}
		return '&nbsp;';
	}
}
?>