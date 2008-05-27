<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../../../course/course_user_category_table/default_course_user_category_table_column_model.class.php';
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
	 * @return CourseUserCategoryTableColumn
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
