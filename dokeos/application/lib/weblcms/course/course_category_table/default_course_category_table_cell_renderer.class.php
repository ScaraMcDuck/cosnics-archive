<?php
/**
 * @package application.lib.weblcms.course.coursecategory_table
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../course_category.class.php';

class DefaultCourseCategoryTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultCourseCategoryTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param CourseCategoryTableColumnModel $column The column which should be
	 * rendered
	 * @param Course Category $coursecategory The course category to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $coursecategory)
	{
		switch ($column->get_name())
		{
			case CourseCategory :: PROPERTY_ID :
				return $coursecategory->get_id();
			case CourseCategory :: PROPERTY_NAME :
				return $coursecategory->get_name();
			case CourseCategory :: PROPERTY_CODE :
				return $coursecategory->get_code();
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