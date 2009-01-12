<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../help_item.class.php';
/**
 * TODO: Add comment
 */
class DefaultHelpItemTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultHelpItemTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $help_item)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case HelpItem :: PROPERTY_NAME :
					return $help_item->get_name();
				case HelpItem :: PROPERTY_URL :
					return $help_item->get_url();
			}
		}
		return '&nbsp;';
	}
}
?>