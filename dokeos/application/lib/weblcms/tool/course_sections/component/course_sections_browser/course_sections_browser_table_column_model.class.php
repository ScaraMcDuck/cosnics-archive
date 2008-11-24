<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../../../../course/course_sections_table/default_course_sections_table_column_model.class.php';
/**
 * Table column model for the course browser table
 */
class CourseSectionsBrowserTableColumnModel extends DefaultCourseSectionsTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function CourseSectionsBrowserTableColumnModel()
	{
		parent :: __construct();
		//$this->set_default_order_column(1);
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
			self :: $modification_column = new ObjectTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
