<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/recycle_bin_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/recycle_bin_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/recycle_bin_browser_table_cell_renderer.class.php';
/**
 * This class provides the table to display all learning objects in the recycle
 * bin.
 */
class RecycleBinBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'repository_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function RecycleBinBrowserTable($browser, $parameters, $condition)
	{
		$model = new RecycleBinBrowserTableColumnModel();
		$renderer = new RecycleBinBrowserTableCellRenderer($browser);
		$data_provider = new RecycleBinBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, RecycleBinBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[RepositoryManager :: PARAM_RESTORE_SELECTED] = Translation :: get('RestoreSelected');
		$actions[RepositoryManager :: PARAM_DELETE_SELECTED] = Translation :: get('DeleteSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>