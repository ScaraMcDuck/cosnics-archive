<?php
/**
 * @remote_package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once Path :: get_admin_path() . '/lib/remote_package.class.php';
/**
 * TODO: Add comment
 */
class DefaultRemotePackageTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultRemotePackageTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $remote_package)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
//				case RemotePackage :: PROPERTY_SECTION :
//					return $remote_package->get_section();
				case RemotePackage :: PROPERTY_NAME :
					return $remote_package->get_name();
				case RemotePackage :: PROPERTY_VERSION :
				    return $remote_package->get_version();
				case RemotePackage :: PROPERTY_DESCRIPTION :
				    return $remote_package->get_description();
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