<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../rights_template.class.php';
/**
 * TODO: Add comment
 */
class DefaultRightsTemplateTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultRightsTemplateTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $rights_template)
	{
		switch ($column->get_name())
		{
			case RightsTemplate :: PROPERTY_NAME :
				return $rights_template->get_name();
			case RightsTemplate :: PROPERTY_DESCRIPTION :
				$description = strip_tags($rights_template->get_description());
				return DokeosUtilities::truncate_string($description,203);
			default :
			    return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>