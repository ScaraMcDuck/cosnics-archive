<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../location.class.php';
/**
 * TODO: Add comment
 */
class DefaultLocationTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultLocationTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $content_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $location)
	{
		switch ($column->get_name())
		{
			case Location :: PROPERTY_LOCATION :
				return $location->get_location();
			case Location :: PROPERTY_TYPE :
				return $location->get_type();
			case Location :: PROPERTY_LOCKED :
				return $location->get_locked();
			case Location :: PROPERTY_INHERIT :
				return $location->get_inherit();
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