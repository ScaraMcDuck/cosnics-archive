<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../../../course/course_user_category_table/course_user_category_table.class.php';
require_once dirname(__FILE__).'/course_user_category_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/course_user_category_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/course_user_category_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Table to display a set of course user categories.
 */
class CourseUserCategoryBrowserTable extends CourseUserCategoryTable
{
	/**
	 * Constructor
	 */
	function CourseUserCategoryBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new CourseUserCategoryBrowserTableColumnModel();
		$renderer = new CourseUserCategoryBrowserTableCellRenderer($browser);
		$data_provider = new CourseUserCategoryBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_default_row_count(20);
	}
}
?>