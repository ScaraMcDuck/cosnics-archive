<?php
/**
 * @package application.lib.weblcms.course.course_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../course_section.class.php';

class DefaultCourseSectionsTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultCourseSectionsTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return CourseSectionTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		//$columns[] = new ObjectTableColumn(CourseSection :: PROPERTY_ID);
		$columns[] = new StaticTableColumn(Translation :: get(DokeosUtilities :: underscores_to_camelcase(CourseSection :: PROPERTY_NAME)));
		return $columns;
	}
}
?>