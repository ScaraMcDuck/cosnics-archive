<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../course/course_table/coursetable.class.php';
require_once dirname(__FILE__).'/admincoursebrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/admincoursebrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/admincoursebrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Table to display a set of learning objects.
 */
class AdminCourseBrowserTable extends CourseTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function AdminCourseBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new AdminCourseBrowserTableColumnModel();
		$renderer = new AdminCourseBrowserTableCellRenderer($browser);
		$data_provider = new AdminCourseBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$actions = array();
		$actions[Weblcms :: PARAM_REMOVE_SELECTED] = get_lang('RemoveSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>