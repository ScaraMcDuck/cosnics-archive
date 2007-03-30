<?php
/**
 * @package repository.courseusercategorytable
 */
require_once dirname(__FILE__).'/courseusercategorytablecolumnmodel.class.php';
require_once dirname(__FILE__).'/courseusercategorytablecolumn.class.php';
require_once dirname(__FILE__).'/../courseusercategory.class.php';

/**
 * TODO: Add comment
 */
class DefaultCourseUserCategoryTableColumnModel extends CourseUserCategoryTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultCourseUserCategoryTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new CourseUserCategoryTableColumn(CourseUserCategory :: PROPERTY_TITLE, true);
		return $columns;
	}
}
?>