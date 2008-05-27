<?php
/**
 * @package application.lib.weblcms.course.courseusercategory_table
 */

require_once dirname(__FILE__).'/course_user_category_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../course_user_category.class.php';

class DefaultCourseUserCategoryTableCellRenderer implements CourseUserCategoryTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultCourseUserCategoryTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param CourseUserCategoryTableColumnModel $column The column which should be
	 * rendered
	 * @param Course user category $courseusercategory The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $courseusercategory)
	{
		if ($property = $column->get_course_user_category_property())
		{
			switch ($property)
			{
				case CourseUserCategory :: PROPERTY_ID :
					return $courseusercategory->get_id();
				case CourseUserCategory :: PROPERTY_TITLE :
					return $courseusercategory->get_title();
			}
		}
		return '&nbsp;';
	}
}
?>