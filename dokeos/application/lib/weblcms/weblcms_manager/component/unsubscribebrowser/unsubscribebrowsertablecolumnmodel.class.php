<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../course/course_table/defaultcoursetablecolumnmodel.class.php';
/**
 * Table column model for the course browser table
 */
class UnsubscribeBrowserTableColumnModel extends DefaultCourseTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function UnsubscribeBrowserTableColumnModel()
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
			self :: $modification_column = new CourseTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
