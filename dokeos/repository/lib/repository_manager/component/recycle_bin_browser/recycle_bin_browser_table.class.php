<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../learning_object_table/learning_object_table.class.php';
require_once dirname(__FILE__).'/recycle_bin_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/recycle_bin_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/recycle_bin_browser_table_cell_renderer.class.php';
/**
 * This class provides the table to display all learning objects in the recycle
 * bin.
 */
class RecycleBinBrowserTable extends LearningObjectTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function RecycleBinBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new RecycleBinBrowserTableColumnModel();
		$renderer = new RecycleBinBrowserTableCellRenderer($browser);
		$data_provider = new RecycleBinBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[RepositoryManager :: PARAM_RESTORE_SELECTED] = Translation :: get('RestoreSelected');
		$actions[RepositoryManager :: PARAM_DELETE_SELECTED] = Translation :: get('DeleteSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>