<?php
/**
 * @package application.lib.weblcms.course.course_table
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../course_section.class.php';

class DefaultCourseSectionsTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultCourseSectionsTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param CourseSectionSectionsTableColumnModel $column The column which should be
	 * rendered
	 * @param CourseSection $course The course object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $course)
	{
		if ($property = $column->get_title())
		{
			switch ($property)
			{
				case Translation :: get(ucfirst(CourseSection :: PROPERTY_NAME)) :
					return $course->get_name();
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