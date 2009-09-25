<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../group_rel_user.class.php';
/**
 * TODO: Add comment
 */
class DefaultGroupRelUserTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultGroupRelUserTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $content_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $groupreluser)
	{
		switch ($column->get_name())
		{
			case GroupRelUser :: PROPERTY_USER_ID :
				return $groupreluser->get_user_id();
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