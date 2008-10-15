<?php
/**
 * @package application.lib.weblcms.course.course_table
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../course.class.php';

class DefaultCourseTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultCourseTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param CourseTableColumnModel $column The column which should be
	 * rendered
	 * @param Course $course The course object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $course)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case Course :: PROPERTY_ID :
					return $course->get_id();
				case Course :: PROPERTY_VISUAL :
					return $course->get_visual();
				case Course :: PROPERTY_NAME :
					return $course->get_name();
				case Course :: PROPERTY_TITULAR :
					return $course->get_titular();
				case Course :: PROPERTY_LANGUAGE :
					return $course->get_language();
				case Course :: PROPERTY_SUBSCRIBE_ALLOWED :
					return $course->get_subscribe_allowed();
				case Course :: PROPERTY_UNSUBSCRIBE_ALLOWED :
					return $course->get_unsubscribe_allowed();
				case Course :: PROPERTY_CATEGORY :
					return $course->get_category();
			}
		}
		return '&nbsp;';
	}
}
?>