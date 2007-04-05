<?php
/**
 * @package repository.coursetable
 */

require_once dirname(__FILE__).'/coursetablecellrenderer.class.php';
require_once dirname(__FILE__).'/../course.class.php';
/**
 * TODO: Add comment
 */
class DefaultCourseTableCellRenderer implements CourseTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultCourseTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $course)
	{
		if ($property = $column->get_course_property())
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
				case Course :: PROPERTY_CATEGORY_CODE :
					return $course->get_category_code();
			}
		}
		return '&nbsp;';
	}
}
?>