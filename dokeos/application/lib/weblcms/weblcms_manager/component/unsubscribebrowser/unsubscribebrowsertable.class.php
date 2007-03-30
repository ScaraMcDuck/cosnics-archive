<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../course/course_table/coursetable.class.php';
require_once dirname(__FILE__).'/unsubscribebrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/unsubscribebrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/unsubscribebrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Table to display a set of learning objects.
 */
class UnsubscribeBrowserTable extends CourseTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function UnsubscribeBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new UnsubscribeBrowserTableColumnModel();
		$renderer = new UnsubscribeBrowserTableCellRenderer($browser);
		$data_provider = new UnsubscribeBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_default_row_count(20);
	}
}
?>