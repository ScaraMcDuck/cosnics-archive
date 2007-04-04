<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../course/coursecategory_table/coursecategorytable.class.php';
require_once dirname(__FILE__).'/coursecategorybrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/coursecategorybrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/coursecategorybrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Table to display a set of learning objects.
 */
class CourseCategoryBrowserTable extends CourseCategoryTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function CourseCategoryBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new CourseCategoryBrowserTableColumnModel();
		$renderer = new CourseCategoryBrowserTableCellRenderer($browser);
		$data_provider = new CourseCategoryBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_default_row_count(20);
	}
}
?>