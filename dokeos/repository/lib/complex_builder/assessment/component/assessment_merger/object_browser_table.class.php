<?php
/**
 * $Id: repository_browser_table.class.php 22935 2009-08-25 12:16:30Z vanpouckesven $
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/object_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/object_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/object_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class ObjectBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'repository_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function ObjectBrowserTable($browser, $parameters, $condition)
	{
		$model = new ObjectBrowserTableColumnModel();
		$renderer = new ObjectBrowserTableCellRenderer($browser);
		$data_provider = new ObjectBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, ObjectBrowserTable :: DEFAULT_NAME, $model, $renderer);
		
		$actions = array();
		$actions[] = new ObjectTableFormAction(AssessmentBuilder :: PARAM_ADD_SELECTED_QUESTIONS, Translation :: get('AddSelectedQuestions'), false);
		$this->set_form_actions($actions);
		
		$this->set_additional_parameters($parameters);
		$this->set_default_row_count(20);
	}
}
?>