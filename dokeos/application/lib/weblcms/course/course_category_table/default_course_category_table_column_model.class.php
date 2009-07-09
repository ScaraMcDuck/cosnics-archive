<?php
/**
 * @package application.lib.weblcms.course.coursecategory_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../course_category.class.php';

class DefaultCourseCategoryTableColumnModel extends ObjectTableColumnModel
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
		$columns[] = new ObjectTableColumn(CourseCategory :: PROPERTY_NAME);
		$columns[] = new ObjectTableColumn(CourseCategory :: PROPERTY_CODE);
		return $columns;
	}
}
?>