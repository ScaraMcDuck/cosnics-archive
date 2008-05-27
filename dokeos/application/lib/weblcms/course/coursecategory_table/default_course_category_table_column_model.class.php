<?php
/**
 * @package application.lib.weblcms.course.coursecategory_table
 */
require_once dirname(__FILE__).'/course_category_table_column_model.class.php';
require_once dirname(__FILE__).'/course_category_table_column.class.php';
require_once dirname(__FILE__).'/../course_category.class.php';

class DefaultCourseCategoryTableColumnModel extends CourseCategoryTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultCourseCategoryTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	/**
	 * Gets the default columns for this model
	 * @return CourseCategoryTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new CourseCategoryTableColumn(CourseCategory :: PROPERTY_NAME, true);
		$columns[] = new CourseCategoryTableColumn(CourseCategory :: PROPERTY_CODE, true);
		return $columns;
	}
}
?>