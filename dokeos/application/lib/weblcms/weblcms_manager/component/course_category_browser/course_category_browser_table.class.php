<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/course_category_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/course_category_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/course_category_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../weblcms_manager.class.php';
/**
 * Table to display a set of course categories.
 */
class CourseCategoryBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'course_category_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function CourseCategoryBrowserTable($browser, $parameters, $condition)
	{
		$model = new CourseCategoryBrowserTableColumnModel();
		$renderer = new CourseCategoryBrowserTableCellRenderer($browser);
		$data_provider = new CourseCategoryBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, CourseCategoryBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_default_row_count(20);
	}
}
?>