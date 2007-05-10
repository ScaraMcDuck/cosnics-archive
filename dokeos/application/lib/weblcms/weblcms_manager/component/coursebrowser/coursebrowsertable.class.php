<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../../../course/course_table/coursetable.class.php';
require_once dirname(__FILE__).'/coursebrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/coursebrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/coursebrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Table to display a set of courses.
 */
class CourseBrowserTable extends CourseTable
{
	/**
	 * Constructor
	 */
	function CourseBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new CourseBrowserTableColumnModel();
		$renderer = new CourseBrowserTableCellRenderer($browser);
		$data_provider = new CourseBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_default_row_count(20);
	}
}
?>