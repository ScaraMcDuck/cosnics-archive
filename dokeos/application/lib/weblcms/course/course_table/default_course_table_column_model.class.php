<?php
/**
 * @package application.lib.weblcms.course.course_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../course.class.php';

class DefaultCourseTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultCourseTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return CourseTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(Course :: PROPERTY_VISUAL);
		$columns[] = new ObjectTableColumn(Course :: PROPERTY_NAME);
		return $columns;
	}
}
?>