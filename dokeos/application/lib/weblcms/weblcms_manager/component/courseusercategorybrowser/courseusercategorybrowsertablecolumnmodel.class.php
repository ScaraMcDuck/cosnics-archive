<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../course/courseusercategory_table/defaultcourseusercategorytablecolumnmodel.class.php';
/**
 * Table column model for the courseusercategory browser table
 */
class CourseUserCategoryBrowserTableColumnModel extends DefaultCourseUserCategoryTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function CourseUserCategoryBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(0);
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new CourseUserCategoryTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
