<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once Path :: get_admin_path() . '/lib/registration.class.php';
/**
 * TODO: Add comment
 */
class DefaultRegistrationTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultRegistrationTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $registration)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case Registration :: PROPERTY_TYPE :
					return Translation :: get(DokeosUtilities :: underscores_to_camelcase($registration->get_type()));
				case Registration :: PROPERTY_NAME :
					return DokeosUtilities :: underscores_to_camelcase_with_spaces($registration->get_name());
				case Registration :: PROPERTY_STATUS :
				    $is_active = $registration->is_active();
				    return Theme :: get_image('action_' . ($is_active ? 'active' : 'inactive'));
			}
		}
		return '&nbsp;';
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>