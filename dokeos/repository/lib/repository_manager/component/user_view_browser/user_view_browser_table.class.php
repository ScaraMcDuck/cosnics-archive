<?php
/**
 * $Id: user_view_browser_table.class.php 17558 2009-01-07 11:37:10Z vanpouckesven $
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/user_view_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/user_view_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/user_view_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../repository_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class UserViewBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'user_view_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function UserViewBrowserTable($browser, $parameters, $condition)
	{
		$model = new UserViewBrowserTableColumnModel();
		$renderer = new UserViewBrowserTableCellRenderer($browser);
		$data_provider = new UserViewBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, UserViewBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		//$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>