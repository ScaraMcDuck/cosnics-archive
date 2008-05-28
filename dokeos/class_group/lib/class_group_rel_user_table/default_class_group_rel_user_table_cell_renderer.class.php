<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../class_group_rel_user.class.php';
/**
 * TODO: Add comment
 */
class DefaultClassGroupRelUserTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultClassGroupRelUserTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $classgroupreluser)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case 'User' :
					return $classgroupreluser->get_user_id();
			}
		}
		return '&nbsp;';
	}
}
?>