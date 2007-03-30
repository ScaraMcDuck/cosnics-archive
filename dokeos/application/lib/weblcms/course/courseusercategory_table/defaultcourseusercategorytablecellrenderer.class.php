<?php
/**
 * @package repository.courseusercategorytable
 */

require_once dirname(__FILE__).'/courseusercategorytablecellrenderer.class.php';
require_once dirname(__FILE__).'/../courseusercategory.class.php';
/**
 * TODO: Add comment
 */
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
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
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