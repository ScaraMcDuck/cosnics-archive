<?php
/**
 * @package repository.coursecategorytable
 */

require_once dirname(__FILE__).'/coursecategorytablecellrenderer.class.php';
require_once dirname(__FILE__).'/../coursecategory.class.php';
/**
 * TODO: Add comment
 */
class DefaultCourseCategoryTableCellRenderer implements CourseCategoryTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultCourseCategoryTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $coursecategory)
	{
		if ($property = $column->get_course_category_property())
		{
			switch ($property)
			{
				case CourseCategory :: PROPERTY_ID :
					return $coursecategory->get_id();
				case CourseCategory :: PROPERTY_NAME :
					return $coursecategory->get_name();
				case CourseCategory :: PROPERTY_CODE :
					return $coursecategory->get_code();
			}
		}
		return '&nbsp;';
	}
}
?>