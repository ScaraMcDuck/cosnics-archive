<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../../../course/course_table/default_course_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../course/course.class.php';
/**
 * Table column model for the course browser table
 */
class AdminCourseBrowserTableColumnModel extends DefaultCourseTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function AdminCourseBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->add_column(new CourseTableColumn(Course :: PROPERTY_LANGUAGE, true));
		$this->add_column(new CourseTableColumn(Course :: PROPERTY_CATEGORY_CODE, true));
		$this->add_column(new CourseTableColumn(Course :: PROPERTY_SUBSCRIBE_ALLOWED, true));
		$this->add_column(new CourseTableColumn(Course :: PROPERTY_UNSUBSCRIBE_ALLOWED, true));
		$this->set_default_order_column(0);
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return CourseTableColumn
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
